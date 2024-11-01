@extends('layouts.app')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Lead Detail</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li>
                            <li class="breadcrumb-item"><a href="{{route('user.leads.index')}}">Lead</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        @include('includes.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="body">
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                        <div class="product details">
                                            <h3 class="product-title mb-0">{{$lead->title}}</h3>
                                            <div class="price mt-0">Lead ID:
                                                <span class="col-amber">{{ '#'.$lead->id }}</span></div>
                                            <hr class="border-info">
                                            <div class="product-description border p-3 mb-4">{!! ($lead->details) ? html_entity_decode($lead->details) : "No Lead Description" !!}</div>
                                            <div class="row">
                                                <div class="col-xl-4 col-lg-4 col-md-4">
                                                    <div class="border p-3">
                                                        <div>
                                                            <span class="zmdi zmdi-account" title="Contact"></span> {{ $lead->name }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-phone" title="Telephone"></span> {{ $lead->phone }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-email" title="Email"></span> {{ $lead->email }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-city" title="City"></span> {{ $lead->lead_city }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-globe-alt" title="State"></span> {{ $lead->lead_state }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-globe" title="County"></span> {{ $lead->lead_country }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-globe-lock" title="IP"></span> {{ $lead->lead_ip }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-lg-4 col-md-4">
                                                    <div class="border p-3">
                                                        <div>
                                                            <span class="zmdi zmdi-money" title="Value"></span> {{ ($lead->value) ? $lead->value.'.00' : '---' }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-check" title="Status"></span> {{ $lead->statusName }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-calendar" title="Date Created"></span> {{ $lead->created_at->format('j F, Y') }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-tag" title="Keyword"></span> {{ $lead->keyword }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-receipt" title="Match Type"></span> {{ $lead->matchtype }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-windows" title="MS Click ID"></span> {{ $lead->msclkid }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-google" title="Google Click ID"></span> {{ $lead->gclid }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-lg-4 col-md-4">
                                                    <div class="border p-3">
                                                        <div>
                                                            <span class="zmdi zmdi-cast-connected" title="Source"></span> {{ ($lead->source) ? $lead->source : "---" }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-blogger" title="Brand"></span> {{ $lead->brandName }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-blogger" title="Brand URL"></span> {{ $lead->brandUrl }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-globe" title="URL"></span> {{ $lead->lead_url }}
                                                        </div>
                                                        <hr class="border-info my-2">
                                                    </div>
                                                    @if($lead->more_details)
                                                        <a title="More Details" href="javascript:void(0)" class=" btn btn-neutral btn-sm btn-round" data-toggle="modal" data-target="#leadModal"> More Details
                                                            <i class="zmdi zmdi-more"></i> </a>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="action mt-3">
                                                {{--@if(Auth::user()->type == 'lead')--}}
                                                @if($lead->status != 2)
                                                    <button id="createInvoice" data-id="{{$lead->id}}" title="Create Team Member" class="btn btn-info btn-round" type="button" data-toggle="modal" data-target="#invoiceModal">
                                                        <span class="zmdi zmdi-assignment-account"></span> Create Invoice
                                                    </button>
                                                @endif
                                                {{--@endif--}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .lead-detail {
            margin-bottom: 10px;
        }

        .lead-key {
            font-weight: bold;
        }

        .lead-value {
            margin-left: 10px;
        }
    </style>
    <div class="modal fade" id="leadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">{{$lead->title}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="leadDetailTable" class="table table-striped table-hover xjs-basic-example theme-color">
                            <thead>
                            <tr>
                                <th>Attribute</th>
                                <th>Value</th>
                            </tr>
                            </thead>
                            <tbody>
                                @if($lead->more_details)
                                    @foreach(json_decode($lead->more_details,true) as $key => $value)
                                        <tr>
                                            @if($key != 'file')
                                                <td class="lead-key">{{ucwords(str_replace('_', ' ', $key))}}:</td>
                                                <td class="lead-value">{{ is_array($value) ? json_encode($value) : $value }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Invoice -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create Invoice</h4>
                </div>
                <form method="POST" id="invoice-Form">
                    <input type="hidden" id="lead_id" class="form-control" name="lead_id" value="{{$lead->id }}">
                    <input type="hidden" id="team_key" class="form-control" name="team_key" value="{{$lead->team_key }}">
                    <input type="hidden" id="brand_key" class="form-control" name="brand_key" value="{{$lead->brand_key }}">
                    <input type="hidden" id="details" class="form-control" name="details" value="{{$lead->details }}">
                    <input type="hidden" id="brand_url" class="form-control" name="brand_url" value="{{$lead->brandUrl }}">
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input type="text" id="name" class="form-control" placeholder="Name" name="name" value="{{ $lead->name }}">
                            </div>
                            <div class="form-group">
                                <input type="email" id="email" class="form-control" placeholder="Email" name="email" value="{{ $lead->email }}"/>
                            </div>
                            <div class="form-group">
                                <input type="text" id="" class="form-control" placeholder="Phone" name="phone" value="{{ $lead->phone }}"/>
                            </div>
                            {{--
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                    </div>
                                    <input type="number" id="" class="form-control" placeholder="Amount" name="value" value="{{ $lead->value }}" required />
                                </div>
                            </div>
                            --}}
                            <div class="form-group">
                                <select id="type" name="agent" class="form-control" data-placeholder="Sale Agent" required>
                                    @foreach($agentSales as $agent)
                                        <option value="{{$agent->id}}">{{$agent->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Project Title</label>
                                <input type="text" id="projectTitle" class="form-control" placeholder="Project Title" name="project_title" value="{{$lead->title}}"/>
                            </div>
                            <div class="form-group">
                                <textarea id="invoice_description" class="form-control xsummernote" placeholder="Description & Details" name="description">{{ strip_tags(str_replace("</li>", "\n", html_entity_decode($lead->details))) }}</textarea>
                            </div>
                            <!--  -->
                            <div class="form-group">
                                <select name="cur_symbol" class="form-control" id="cur_symbol">
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                    <option value="AUD">AUD</option>
                                    <option value="CAD">CAD</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text cxm-currency-symbol-icon"><i class="zmdi zmdi-money"></i></span>
                                    </div>
                                    <input type="number" id="amount" class="form-control" placeholder="Amount" name="value" max=2499' required/>
                                </div>
                            </div>
                            {{--<div class="form-group">
                                <label class="text-muted">
                                    <input type="checkbox" name="taxable" id="taxable" value="1" checked="">
                                    Taxable?
                                </label>
                            </div>--}}
                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input toggle-class" id="taxable" name="taxable" value="1" checked>
                                <label class="custom-control-label" for="taxable">Taxable?</label>
                            </div>
                            <div class="form-group" id="taxField">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">%</i></span>
                                    </div>
                                    <input type="hidden" id="tax_amount" class="form-control" name="taxAmount" value="0">
                                    <input type="number" name="tax" id="tax" class="form-control" placeholder="Tax"/>
                                </div>
                            </div>
                            <div class="form-group" id="totalAmount">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text cxm-currency-symbol-icon"><i class="zmdi zmdi-money"></i></span>
                                    </div>
                                    <input type="text" name="total_amount" class="form-control" placeholder="Total Amount" id="total_amount" value="0" readonly>
                                </div>
                            </div>
                            <!--  -->
                            <div class="form-group">
                                <input type="date" id="due_date" class="form-control" placeholder="Due Date" name="due_date" required/>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-round">SAVE</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('cxmScripts')
    @include('lead.script')
    @include('includes.currency-change')
    <script>
        $(document).ready(function () {
            $('#leadDetailTable').DataTable();
        });
    </script>
@endpush
