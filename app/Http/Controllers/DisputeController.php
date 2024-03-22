<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateInvoiceRequest;
use App\Mail\DisputeMail;
use App\Models\Dispute;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DisputeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $disputes = Dispute::when(auth()->user()->role == User::ROLE_USER, function ($query) {
            $query->whereHas('invoice', function ($query) {
                $query->where('user_id', auth()->user()->id);
            });
        })
            ->with('invoice.user')
            ->paginate(10);
        return view('disputes', compact('disputes'));
    }


    public function store(ValidateInvoiceRequest $request)
    {
        $invoice = Invoice::findOrFail($request->invoice_id);
        if ($invoice->status == Invoice::STATUS_PAID) {
            $dispute = Dispute::create([
                'invoice_id' => $invoice->id,
            ]);

            if (!$dispute)
                return redirect()->back()->with('status', "You cant file dispute on this order");
            $mail = User::where('role', 'admin')->first()?->email;
            Mail::to($mail)->send(new DisputeMail($dispute));
            return redirect(route('disputes.index'))->with('status', "Dispute filed");
        }
        return redirect()->back()->with('status', "You cant file dispute on this order");
    }


    public function update(Request $request, Dispute $dispute)
    {
        $dispute->update([
            'status' => $request->status,
        ]);
        if ($request->status == Dispute::STATUS_RESOLVED)
            $dispute->invoice->update([
                'status' => Invoice::STATUS_DISPUTED,
            ]);

        Mail::to($dispute->invoice->user->email)->send(new DisputeMail($dispute, true));
        return redirect()->back()->with('status', "Status changed");
    }
}
