<!DOCTYPE html>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
    <script src="//cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.0/moment.min.js"></script>
    <style>
        .ct-label.ct-horizontal { position: relative; transform: rotate(90deg); transform-origin: left top; height: 100px;}
    </style>
</head>
<body>
<div class="ct-chart ct-golden-section" id="chart1"></div>

<script>

    var chart = new Chartist.Line('.ct-chart', {
        height: 400,
        series: [
            {
                name: 'series-1',
                data: [
                    @foreach ($rows as $row)
                        {x: new Date({{ strtotime($row->date_formatted)*1000 }}), y: {{ $row->published_total}} },
                    @endforeach
                ]
            }
        ]
    }, {
        axisX: {
            type: Chartist.FixedScaleAxis,
            divisor: {{ count($rows) }},
            labelInterpolationFnc: function(value) {
                return moment(value).format('D/MM/YY');
            }
        }
    });
;
</script>
</body>
</html>
