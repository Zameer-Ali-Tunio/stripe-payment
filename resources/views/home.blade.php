@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if(session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <div class="container">
                        @if (auth()->user()->role == 'user')

                        <a href="{{route('home.create')}}" class="btn btn-primary">Create Invoice</a>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-bordered   table-striped" style="margin-top:100px">
                                <thead class="table__head">
                                    <tr class="winner__table">
                                        <th>S/N</th>
                                        <th><i aria-hidden="true"></i> Transaction ID</th>
                                        <th><i class="fa fa-user" aria-hidden="true"></i> User</th>
                                        <th><i class="fa fa-dollar" aria-hidden="true"></i> Amount</th>
                                        <th><i class="fa fa-calendar-o" aria-hidden="true"></i> Date</th>
                                        <th> <i class="fa fa-phone" aria-hidden="true"></i> Status</th>
                                        @if (auth()->user()->role == 'user')
                                        <th><i class="fa fa-trophy" aria-hidden="true"></i> Actions</th>

                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $id => $invoice)
                                    <tr class="winner__table">
                                        <td>{{ $id+1 }}</td>
                                        <td>{{ $invoice->transaction_id ?? 'Not Found' }}</td>
                                        <td>{{ $invoice->user->name }}</td>
                                        <td>{{ $invoice->amount }}</td>
                                        <td>{{ $invoice->due_date }}</td>
                                        <td>{{ $invoice->status }}</td>
                                        @if (auth()->user()->role == 'user')

                                        <th>
                                            <form id="dispute-form" action="{{ route('disputes.store') }}" method="POST" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
                                                <button type="submit" class="btn btn-primary">Dispute</button>
                                            </form>

                                            <form id="refund-form" action="{{ route('refunds.store') }}" method="POST" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
                                                <button type="submit" class="btn btn-primary">Refund</button>
                                            </form>

                                        </th>
                                        @endif
                                    </tr>
                                    @empty
                                    <tr class="winner__table">
                                        <td colspan="7" class="text-center">No Record Found</td>
                                    </tr>
                                    @endforelse



                                </tbody>
                            </table>
                            {{ $invoices->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
