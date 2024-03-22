<?php

namespace App\Http\Controllers;

use App\Mail\SuccessfulPaymentMail;
use App\Models\Invoice;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }
    public function index()
    {
        $invoices = Invoice::when(auth()->user()->role == User::ROLE_USER, function ($query) {
            $query->where('user_id', auth()->user()->id);
        })->with('user')->paginate(10);
        return view('home', compact('invoices'));
    }
    public function create()
    {
        try {
            $user = auth()->user();
            if (!$user->stripe_id)
                $user->createAsStripeCustomer();

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'unit_amount' => 10000,
                            'product_data' => [
                                'name' => 'Your Product Name',
                            ],
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => url("success_payment?session_id={CHECKOUT_SESSION_ID}"),
                'cancel_url' => url("cancel_payment?session_id={CHECKOUT_SESSION_ID}"),
                'customer' => $user->stripe_id,
            ]);

            return redirect($session->url);
        } catch (Exception $e) {
            return back()->with('status', $e->getMessage());
        }
    }
    public function successPayment(Request $request)
    {
        try {
            $user = auth()->user();
            $sessionId = $request->query('session_id');
            if ($sessionId) {
                $sessionId = $request->query('session_id');
                $session = \Stripe\Checkout\Session::retrieve($sessionId);
                $paymentStatus = $session->payment_status;
                $invoice = Invoice::create([
                    'user_id' => $user->id,
                    'transaction_id' => $session->payment_intent,
                    'amount' => $session->amount_total / 100,
                    'due_date' => now(),
                    'status' => $paymentStatus == Invoice::STATUS_PAID ? Invoice::STATUS_PAID : Invoice::STATUS_PENDING,
                ]);
                if (!$invoice)
                    return redirect(route('home.index'))->with('status', 'INvoice could not create');

                Mail::to($user->email)->send(new SuccessfulPaymentMail($invoice));
            } else {
                return redirect(route('home.index'))->with('status', 'Error');
            }
            return redirect(route('home.index'))->with('status', 'Payment completed');
        } catch (Exception $e) {
            return redirect(route('home.index'))->with('status', $e->getMessage());
        }
    }
    public function cancelPayment(Request $request)
    {
        try {
            $user = auth()->user();
            $sessionId = $request->query('session_id');
            if ($sessionId) {
                $sessionId = $request->query('session_id');
                $session = \Stripe\Checkout\Session::retrieve($sessionId);
                $paymentStatus = $session->payment_status;
                Invoice::create([
                    'user_id' => $user->id,
                    'transaction_id' => $session->payment_intent ?? null,
                    'amount' => $session->amount_total / 100,
                    'due_date' => now(),
                    'status' => $paymentStatus == Invoice::STATUS_PAID ? Invoice::STATUS_PAID : Invoice::STATUS_PENDING,
                ]);
            } else {
                return redirect(route('home.index'))->with('status', 'Error');
            }
            return redirect(route('home.index'))->with('status', 'Payment not completed');
        } catch (Exception $e) {
            return redirect(route('home.index'))->with('status', $e->getMessage());
        }
    }
}
