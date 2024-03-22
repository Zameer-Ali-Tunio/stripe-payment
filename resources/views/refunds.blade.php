@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Refunds') }}</div>

                <div class="card-body">
                    @if(session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="container">
                        <div class="table-responsive">
                            <table class="table table-bordered   table-striped" style="margin-top:100px">
                                <thead class="table__head">
                                    <tr class="winner__table">
                                        <th>S/N</th>
                                        <th><i aria-hidden="true"></i> Invoice</th>
                                        <th><i class="fa fa-user" aria-hidden="true"></i> User</th>
                                        <th><i class="fa fa-dollar" aria-hidden="true"></i> Amount</th>
                                        <th><i class="fa fa-calendar-o" aria-hidden="true"></i> Date</th>
                                        <th> <i class="fa fa-phone" aria-hidden="true"></i> Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($refunds as $id => $refund)
                                    <tr class="winner__table">
                                        <td>{{ $id+1 }}</td>
                                        <td>#{{ $refund->invoice_id}}</td>
                                        <td>{{ $refund->invoice->user->name }}</td>
                                        <td>{{ $refund->invoice->amount }}</td>
                                        <td>{{ $refund->created_at->format("Y-m-d") }}</td>
                                        @if(auth()->user()->role == 'admin')
                                        <td>
                                            @if($refund->status != 'pending')
                                            {{ $refund->status }}
                                            @else
                                            <form id="refund-form" action="{{ route('refunds.update', $refund->id) }}" style="display: inline;" method="post">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" id="refund-status" class="form-control">
                                                    <option value="pending">Pending</option>
                                                    <option value="approved">Approved</option>
                                                    <option value="cancelled">Cancelled</option>
                                                </select>
                                            </form>
                                            @endif
                                        </td>
                                        @else
                                        <td>{{ $refund->status }}</td>

                                        @endif
                                    </tr>
                                    @empty
                                    <tr class="winner__table">
                                        <td colspan="7" class="text-center">No Record Found</td>
                                    </tr>
                                    @endforelse



                                </tbody>
                            </table>
                            {{ $refunds->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var disputeForm = document.getElementById('refund-form');
        var disputeReasonSelect = document.getElementById('refund-status');
        disputeReasonSelect.addEventListener('change', function() {
            disputeForm.submit();
        });
    });
</script>
