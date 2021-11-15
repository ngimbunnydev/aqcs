@extends('backend.layout')
@section('header_import')

@stop



@section('footer_import')

  <script type="text/javascript">
		$(document).ready(function() {

			@if (session('errors'))
				$.gritter.add({
								title: 'Warning:',
								text: '<strong><i class="ace-icon fa fa-exclamation-triangle"></i></strong> {{ session('errors') }}',
								sticky: true,
								class_name: 'gritter-error gritter-center'
							});
			@endif

			@if (session('success'))
				$.gritter.add({
								title: 'Success:',
								text: '<strong><i class="ace-icon fa fa-check-square-o"></i></strong> {{ session('success') }}',
								sticky: false,
								class_name: 'gritter-success gritter-center'
							});

			@endif



		});
	</script>

	<script>
		$( document ).ready(function() {
		  //airWindows('', {ajaxpath:'ajax_obj', objpath:'', ajaxobj:'cleancached', ajaxact:'index'},'','',false);
		});
		//protexlogin()

		

	</script>

@stop


@section('app')
	<h1> DASHBOARD</h1>
	@if(beta())
    <div class="row">
      <div class="col-xs-12 col-sm-2 widget-container-col col-dashboard">
          <div class="widget-box" id="widget-box-7">
            <div class="widget-header widget-header-small">
              <h5 class="widget-title smaller"><strong>@lang('label.lb251')</strong></h5>

              <div class="widget-toolbar">
                <span class="badge badge-info" style="font-size: 10px;line-height:12px;">
                  @lang('label.lb252')
                </span>
              </div>
            </div>

            <div class="widget-body">
              <div class="widget-main">
                <div class="dashboard-summary">
                  <span class="dashboard-data-number">{{ formatmoney($sales_monthly->sale_monthly??0,true) }}</span>
                  <div class="dashboard-content blue text-right">
                    <span class="dashboard-content-text blue"><strong>@lang('label.lb253')&nbsp;&nbsp;</strong></span>
                    <span class="dashboard-content-percentage green">
                      <strong>
                        @php 
                          $sale_monthly = $sales_monthly->sale_monthly ?? 0;
                          $paid_monthly = $sales_monthly->paid_monthly ?? 0;
                          $paid_percent = ($paid_monthly / $sale_monthly) * 100;
                        @endphp
                        {{ number_format($paid_percent, 2) }}%
                      </strong>
                    </span>
                  </div>
                </div>
                <!-- end dashboard summary -->
              </div>
            </div>
          </div>
        </div>  
        <!-- end col -->
    </div>
  @endif
@stop







								

