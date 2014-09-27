<!DOCTYPE html>
<html lang="en">

    <script type="text/javascript" src="<?php FCPATH ?>public/jquery-1.11.1.min.js"></script>
    
    <script type="text/javascript">
    $(function () {
    $('#container').highcharts({
        
        title: {
            text: 'Share Information',
            x: -20 //center
        },
        subtitle: {
            text: 'Mandiri Wealth Management Report',
            x: -20
        },
        xAxis: {
            // categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',]
            categories: [
            <?php 
                foreach($percentage_bmri as $pbmri){
                echo "'".$pbmri['date']."'".","; }?>
            ]
        },
        yAxis: {
            title: {
                text: 'Temperature (°C)'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: '°C'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: 'Tokyo',
            data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
        }, {
            name: 'New York',
            data: [-0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
        }, {
            name: 'Berlin',
            data: [-0.9, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0]
        }, {
            name: 'London',
            data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
        }]
    });
});
</script>

            <?php 
                $arr =  array();
                foreach($percentage_bmri as $pbmri){
                $arr[] = $pbmri['date']; }
                echo implode(', ', $arr);
            ?>

    <?php foreach($percentage_bmri as $pbmri){
        echo $pbmri['date'];
        echo $pbmri['percentage'];
    } ?>
    <?php foreach($percentage_jci as $pjci){
        echo $pjci['date'];
        echo $pjci['percentage'];
    } ?>

    <script type="text/javascript" src="<?php FCPATH ?>public/highcharts/js/highcharts.js"></script>
    <script type="text/javascript" src="<?php FCPATH ?>public/highcharts/js/modules/exporting.js"></script>
    <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

</html>