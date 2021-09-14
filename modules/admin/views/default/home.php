<?php 
    use app\modules\admin\models\Helper;
?>
    
    <style>
    #chartdiv {
        width: 100%;
        height: 500px;
    }
    #chartdiv1 {
        width: 100%;
        height: 500px;
    }

</style>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <!--<a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>-->
    </div>
    <section class="banner" style="visibility: visible;">
        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
            <ol class="carousel-indicators">
                <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></li>
            
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="/img/<?= !Helper::isMobile()?'banner-site-desktop.png':'banner-site-mobile.png'?>" style="cursor:pointer; width: 100%;"/>
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden"></span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden"></span>
            </a>
        </div>
    </section>
    <!-- Color System -->
    <div class="row mt-3 mb-3">
        <div class="col-lg-12 mb-4">
            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Regulamento</h6>
                </div>
                <div class="card-body">
                    <a class="btn btn-md btn-success" href="/pdf/Regulamento - Trote Solidário 2021_2 OK.pdf" target="_blank" title="Hemocentros">
                        Para ver o regulamento clique aqui
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Color System -->
    <div class="row mt-3 mb-3">
        <div class="col-lg-12 mb-4">
            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informativo Doação de Sangue</h6>
                </div>
                <div class="card-body">
                    <p>Para a doação de sangue, o participante deve ir sozinho ao hemocentro (mediante agendamento prévio), em horários alternativos, evitando aglomerações de pessoas. Os interessados não podem se esquecer de usar máscara de proteção e álcool gel. Ir ao hemocentro é um ato de coragem e de bravura em benefício da saúde. O Simers lembra que uma bolsa de sangue pode salvar até quatro vidas, e a doação é fundamental neste momento de restrições (com a diminuição dos estoques devido ao distanciamento social).</p>
                    <a class="btn btn-md btn-success" href="https://drive.google.com/file/d/1M_WZwgE8zIw6eH8smI1Jf8na_nPc5Nj_/view?usp=sharing" id="rd-button-kmpbw2hu" target="_blank" title="Hemocentros">
                        Clique para acessar a relação de hemocentros
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mb-4">
            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informativo Doação de Alimentos</h6>
                </div>
                <div class="card-body">
                    <p>A arrecadação de donativos será realizada de forma online, por meio de links personalizados para cada universidade direcionados ao site do Banco de Alimentos do Estado. Dessa forma, o doador acessa o link da respectiva instituição de ensino que queira contribuir (acesse os links abaixo), seleciona a quantidade e os alimentos que deseja doar e finaliza o pedido. O processo é semelhante ao realizado em compras on-line. Ao final da ação, tudo que for angariado será destinado à instituições carentes. O valor/alimento arrecadado será destinado à cidade sede da universidade. </p>
                    <h3 class="text-center">Clique e escolha a universidade para doar alimentos</h3>
                    <br>
                    <br>
                    <div class="row" style="margin-bottom: 30px;">
                        <div class="col-md-4">
                            <a class="btn btn-md btn-success" style="width: 100%;height:100%;" href="http://bit.ly/TroteUFSM21" target="_blank" title="">
                                UFSM - Universidade Federal Santa Maria
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a class="btn btn-md btn-success" style="width: 100%;height:100%;" href="http://bit.ly/TroteULBRA21" target="_blank" title="">
                                ULBRA - Universidade Luterana do Brasil
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a class="btn btn-md btn-success" style="width: 100%;height:100%;" href="http://bit.ly/TroteUNISINOS21" target="_blank" title="">
                                UNISINOS - Universidade do Vale do Rio dos Sinos
                            </a>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 30px;">
                        <div class="col-md-4">
                            <a class="btn btn-md btn-success" style="width: 100%;height:100%;" href="http://bit.ly/TroteUCS21" target="_blank" title="">
                                UCS - Universidade Caxias do Sul
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a class="btn btn-md btn-success" style="width: 100%;height:100%;" href="http://bit.ly/TroteUPF21" target="_blank" title="">
                                UPF - Universidade de Passo Fundo
                            </a>
                        </div>
                        <div class="col-md-4">

                            <a class="btn btn-md btn-success" style="width: 100%;height:100%;" href="http://bit.ly/TroteUFFS21" target="_blank" title="">
                                UFFS - Universidade Federal da Fronteira do Sul
                            </a>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 30px;">
                        <div class="col-md-4">
                            <a class="btn btn-md btn-success" style="width: 100%;height:100%;" href="http://bit.ly/TroteUFPEL21" target="_blank" title="">
                                UFPEL - Universidade Federal de Pelotas
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a class="btn btn-md btn-success" style="width: 100%;height:100%;" href="http://bit.ly/TroteUNISC21" target="_blank" title="">
                                UNISC - Universidade de Santa Cruz
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a class="btn btn-md btn-success" style="width: 100%;height:100%;" href="http://bit.ly/TroteUFRGS21" target="_blank" title="">
                                UFRGS - Universidade Federal do Rio Grande do Sul
                            </a>

                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 30px;">
                        <div class="col-md-4">
                            <a class="btn btn-md btn-success" style="width: 100%;height:100%;" href="http://bit.ly/TroteUFN21" target="_blank" title="">
                                UFN - Universidade Franciscana
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a class="btn btn-md btn-success" style="width: 100%;height:100%;" href="http://bit.ly/TroteUNIVATES21" target="_blank" title="">
                                UNIVATES - Fundação Vale do Taquari
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a class="btn btn-md btn-success" style="width: 100%;height:100%;" href="http://bit.ly/TroteUNIPAMPA21" target="_blank" title="">
                                UNIPAMPA - Universidade Federal do Pampa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mb-4">
            <!-- Illustrations -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Inscritos</h6>
                </div>
                <div class="card-body">
                    <!-- HTML -->
                    <div id="chartdiv"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

<!-- Chart code -->
<script>
                    am4core.ready(function () {

                        // Themes begin
                        am4core.useTheme(am4themes_animated);
                        am4core.addLicense("ch-custom-attribution");
                        // Themes end

                        // Create chart instance
                        var chart = am4core.create("chartdiv", am4charts.XYChart);

                        // Add data
                        chart.data = <?= $dados ?>;


                        // Create axes
                        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                        categoryAxis.dataFields.category = "name";
                        categoryAxis.renderer.grid.template.disabled = true;
                        categoryAxis.renderer.minGridDistance = 30;
                        categoryAxis.renderer.inside = true;
                        categoryAxis.renderer.labels.template.fill = am4core.color("#fff");
                        categoryAxis.renderer.labels.template.fontSize = 0;

                        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                        valueAxis.renderer.grid.template.strokeDasharray = "4,4";
                        valueAxis.renderer.labels.template.disabled = true;
                        valueAxis.min = 0;

                        // Do not crop bullets
                        chart.maskBullets = false;

                        // Remove padding
                        chart.paddingBottom = 0;

                        // Create series
                        var series = chart.series.push(new am4charts.ColumnSeries());
                        series.dataFields.valueY = "points";
                        series.dataFields.categoryX = "name";
                        series.columns.template.propertyFields.fill = "color";
                        series.columns.template.propertyFields.stroke = "color";
                        series.columns.template.column.cornerRadiusTopLeft = 15;
                        series.columns.template.column.cornerRadiusTopRight = 15;
                        series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

                        // Add bullets
                        var bullet = series.bullets.push(new am4charts.Bullet());
                        var image = bullet.createChild(am4core.Image);
                        image.horizontalCenter = "middle";
                        image.verticalCenter = "bottom";
                        image.dy = 20;
                        image.y = am4core.percent(100);
                        image.propertyFields.href = "bullet";
                        image.tooltipText = series.columns.template.tooltipText;
                        image.propertyFields.fill = "color";
                        image.filters.push(new am4core.DropShadowFilter());

                    }); // end am4core.ready()

</script>
