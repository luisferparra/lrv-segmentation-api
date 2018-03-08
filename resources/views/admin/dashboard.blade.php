 @extends('adminlte::page')

@section('title', 'Dashborad')

@section('content_header')
<h1>Dashboard</h1>
@stop

@section('content')

@if (isset($dashboard_top))
  @foreach ($dashboard_top as $rows)
  <div class="row">
     @foreach ($rows as $item)
     {!! $item !!} 
     @endforeach  
  </div>
  @endforeach
@endif

  @if (isset($charts))
  <div class="box box-default">
      
   
    @foreach ($charts as $rows)
        <div class="row">
            @foreach ($rows as $chart)
              <div class="col-md-4">
                  <div class="result-stats" id="{{ $chart['name'] }}"></div>
              </div>
            @endforeach

        </div>
    @endforeach
      </div>
  @endif




    
      @stop

      @section('js');
<script src="https://unpkg.com/frappe-charts@0.0.8/dist/frappe-charts.min.iife.js"></script>
<script>
 // Javascript Graphs

 @if (isset($charts))
    @foreach ($charts as $rows)
        @foreach ($rows as $chart)
          let {!! $chart['name'] !!} = {
            labels: [{!! $chart['graphLabel'] !!}],
          
            datasets: [{
title: '{{ $chart['label'] }}',values: [{!! $chart['graphData'] !!}]
            }]
           
          };
          let chart{!! $chart['name'] !!} = new Chart({
            parent: "#{{ $chart['name'] }}", // or a DOM element
            title: '{{ $chart['label'] }}',
            data: {!! $chart['name'] !!},
            type: "{{ $chart['type'] }}",
            height: 250,
            colors:[ {!! $chart['colour'] !!} ],
            format_tooltip_x: d => (d + '').toUpperCase(),
            format_tooltip_y: d => d.toLocaleString(),
            axisOptions: {
              yAxisMode: 'span',   // Axis lines, default
              xAxisMode: 'tick',   // No axis lines, only short ticks
              xIsSeries: 10         // Allow skipping x values for space
                                   // default: 0
            }
          });
        @endforeach
    @endforeach
 @endif






</script>
      @stop