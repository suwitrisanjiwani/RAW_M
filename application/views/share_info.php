<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Mandiri Project Prototype</title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php FCPATH ?>public/scrolling-nav/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php FCPATH ?>public/scrolling-nav/css/scrolling-nav.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<!-- The #page-top ID is part of the scrolling feature - the data-spy and data-target are part of the built-in Bootstrap scrollspy function -->

<body id="page-top" data-spy="scroll" data-target=".navbar-fixed-top">
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand page-scroll" href="#page-top"></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav">
                    <!-- Hidden li included to remove active class from about link when scrolled up past about section -->
                    <li class="hidden">
                        <a class="page-scroll" href="#page-top"></a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#about">Share Information</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#services">Early Signs</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#contact">Key Indicators</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#Test">Key Financial Highlights</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#BalancedEarning">Balanced Earnings</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#SRG">Strong Revenue Growth</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#increadingcasa">Increading CASA</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#BOH">Building Our High</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Intro Section -->
    <section id="intro" class="intro-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Demo</h1>
                    
                    <a class="btn btn-default page-scroll" href="#about">Click Me to Scroll Down!</a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Share Information</h1>
                        <div id="container" style="width: 510px; float:left; height: 320px; margin: 0 auto"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Early Signs</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Key Indicators</h1>
                </div>
            </div>
        </div>
    </section>
    
     <!-- Test Section -->
    <section id="Test" class="services-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Key Financial Highlights</h1>
                </div>
            </div>
        </div>
    </section>
    
     <!-- Balanced Earning -->
    <section id="BalancedEarning" class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Balanced Earning</h1>
                </div>
            </div>
        </div>
    </section>
    
     <!-- Strong Revenue Growth Section -->
    <section id="SRG" class="services-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Strong Revenue Growth</h1>
                </div>
            </div>
        </div>
    </section>
    
     <!-- Increading CASA Section -->
    <section id="increadingcasa" class="contact-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Increading CASA Section</h1>
                </div>
            </div>
        </div>
    </section>
    
     <!-- Building Our High Section -->
    <section id="BOH" class="services-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Building Our High Section</h1>
                </div>
            </div>
        </div>
    </section>
    
    <!-- jQuery Version 1.11.1 -->
    <script type="text/javascript" src="<?php FCPATH ?>public/jquery-1.11.1.min.js"></script>
    
    <!-- Bootstrap Core JavaScript -->
    <script src="<?php FCPATH ?>public/scrolling-nav/js/bootstrap.min.js"></script>

    <!-- Scrolling Nav JavaScript -->
    <script src="<?php FCPATH ?>public/scrolling-nav/js/jquery.easing.min.js"></script>
    <script src="<?php FCPATH ?>public/scrolling-nav/js/scrolling-nav.js"></script>
    <script type="text/javascript">
    $(function () {
    $('#container').highcharts({
        
        title: {
            text: 'Mandiri Wealth Management Report',
            x: -20 //center
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
                text: 'Percentage (%)'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
            name: 'BMRI',
            data: [
            <?php 
                foreach($percentage_bmri as $pbmri){
                echo $pbmri['percentage'].","; }?>
            ]
        }, {
            name: 'JCI',
            data: [
            <?php 
                foreach($percentage_jci as $pjci){
                echo $pjci['percentage'].","; }?>
            ]
        }]
    });
});
</script>
    <script type="text/javascript" src="<?php FCPATH ?>public/highcharts/js/highcharts.js"></script>
    <script type="text/javascript" src="<?php FCPATH ?>public/highcharts/js/modules/exporting.js"></script>
    
</body>

</html>
