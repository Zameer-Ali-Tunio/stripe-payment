<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateInvoiceRequest;
use App\Mail\RefundMail;
use App\Models\Invoice;
use App\Models\Refund;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RefundController extends Controller
{
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $refunds = Refund::when(auth()->user()->role == User::ROLE_USER, function ($query) {
            $query->whereHas('invoice', function ($query) {
                $query->where('user_id', auth()->user()->id);
            });
        })
            ->with('invoice.user')
            ->paginate(10);
        return view('refunds', compact('refunds'));
    }


    public function store(ValidateInvoiceRequest $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);
        if ($invoice->status == Invoice::STATUS_PAID) {
            $refund = Refund::create([
                'invoice_id' => $invoice->id,
            ]);
            if (!$refund)
                return redirect()->back()->with('status', "You cant file refund on this order");
            $mail = User::where('role', 'admin')->first()?->email;
            Mail::to($mail)->send(new RefundMail($refund));
            return redirect(route('refunds.index'))->with('status', "Refund filed");
        }
        return redirect()->back()->with('status', "You cant file refund on this order");
    }


    public function update(Request $request, Refund $refund)
    {
        try {
            if ($request->status == Refund::STATUS_APPROVED) {
                $refundStripe = \Stripe\Refund::create([
                    'payment_intent' => $refund->invoice->transaction_id,
                ]);

                if ($refundStripe->status === 'succeeded') {
                    $refund->update([
                        'status' => $request->status,
                    ]);
                    $refund->invoice->update([
                        'status' => "refunded",
                    ]);
                }
            }
            Mail::to($refund->invoice->user->email)->send(new RefundMail($refund, true));
            return redirect()->back()->with('status', "Status changed");
        } catch (Exception $e) {
            return back()->with('status', $e->getMessage());
        }
    }
}
