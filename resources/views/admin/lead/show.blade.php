@extends('admin.layouts.app')@section('cxmTitle', 'Detail')

@section('content')

    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Lead Detail</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li> <li class="breadcrumb-item">Lead</li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-warning btn-round btn-icon right_icon_toggle_btn" type="button">
                            <i class="zmdi zmdi-arrow-right"></i></button>
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
                                            <hr class="border-warning">
                                            <div class="product-description border p-3 mb-3">{!! ($lead->details) ? html_entity_decode($lead->details) : "No Lead Description" !!}</div>
                                            <div class="row">
                                                <div class="col-xl-4 col-lg-4 col-md-4">
                                                    <div class="border p-3">
                                                        <div>
                                                            <span class="zmdi zmdi-account" title="Contact"></span> {{ $lead->name }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-phone" title="Telephone"></span> {{ $lead->phone }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-email" title="Email"></span> {{ $lead->email }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-city" title="City"></span> {{ $lead->lead_city }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-globe-alt" title="State"></span> {{ $lead->lead_state }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-globe" title="County"></span> {{ $lead->lead_country }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-globe-lock" title="IP"></span> {{ $lead->lead_ip }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                    </div>
                                                    {{--<div class="action mt-3">
                                                        <button class="btn btn-warning btn-round" type="button"><i class="zmdi zmdi-account"></i> Convert To Customer</button>
                                                    </div>--}}
                                                </div>
                                                <div class="col-xl-4 col-lg-4 col-md-4">
                                                    <div class="border p-3">
                                                        <div>
                                                            <span class="zmdi zmdi-money" title="Value"></span> {{ ($lead->value) ? '$'.$lead->value.'.00' : '---' }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-check" title="Status"></span> {{ $lead->status }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-calendar" title="Date Created"></span> {{ $lead->created_at->format('j F, Y') }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-tag" title="Keyword"></span> {{ $lead->keyword }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-receipt" title="Match Type"></span> {{ $lead->matchtype }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-windows" title="MS Click ID"></span> {{ $lead->msclkid }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-google" title="Google Click ID"></span> {{ $lead->gclid }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                    </div>
                                                </div>
                                                <div class="col-xl-4 col-lg-4 col-md-4">
                                                    <div class="border p-3">
                                                        <div>
                                                            <span class="zmdi zmdi-cast-connected" title="Source"></span> {{ ($lead->source) ? $lead->source : "---" }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-blogger" title="Brand"></span> {{ $lead->brandName }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        <div>
                                                            <span class="zmdi zmdi-globe" title="URL"></span> {{ $lead->lead_url }}
                                                        </div>
                                                        <hr class="border-warning my-2">
                                                        @if(isset(json_decode($lead->file,true)[0]['original_name']))
                                                            <div>
                                                                <span class="zmdi zmdi-file" title="FILE"></span>
                                                                {{ json_decode($lead->file,true)[0]['original_name'] }}
                                                                <a href="{{ asset('assets/images/leads/' . json_decode($lead->file, true)[0]['mime_type'] . '/' . json_decode($lead->file, true)[0]['file_name']) }}" download="{{ json_decode($lead->file, true)[0]['original_name'] }}" style="color: black;">
                                                                    <span class="zmdi zmdi-download" title="download"></span>
                                                                </a>
                                                            </div>
                                                            <hr class="border-warning my-2">
                                                        @endif
                                                    </div>
                                                    @if($lead->more_details)
                                                        <a title="More Details" href="javascript:void(0)" class=" btn btn-neutral btn-sm btn-round text-warning" data-toggle="modal" data-target="#leadModal"> More Details
                                                            <i class="zmdi zmdi-more"></i> </a>
                                                    @endif
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
                            @if(isset(json_decode($lead->file,true)[0]['original_name']))
                                <tr>
                                    <td class="lead-key">File:</td>
                                    <td class="lead-value">{{ json_decode($lead->file,true)[0]['original_name'] }}</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('cxmScripts')
    @include('admin.lead.script')
    <script>
        $(document).ready(function () {
            $('#leadDetailTable').DataTable();
        });
    </script>
@endpush
