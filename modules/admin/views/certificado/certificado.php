<?php
    use app\modules\admin\models\Helper;
    use yii\helpers\Url;
?>
<style type="text/css">
    .tg  {}
    .tg td{font-family:Arial, sans-serif;font-size:14px;
           overflow:hidden;padding:10px 5px;word-break:normal;}
    .tg th{font-family:Arial, sans-serif;font-size:14px;
           font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}
    .tg .tg-baqh{text-align:center;vertical-align:top}
    .tg .tg-0lax{text-align:left;vertical-align:top}
</style>
<body class="body">
    <div class="well" style="background-color: #2b9879">
        <div class="row">
            <table class="tg" style="table-layout: fixed; width: 100%">
                <thead>
                    <tr>
                        <th class="tg-0lax"></th>
                        <th class="tg-baqh" colspan="3" >
                            <img src="/var\www\html\trotesolidario\web\img\logocertificado.jpg" style="width:auto;height: 180px;" alt="logo" />
                        </th>
                        <th class="tg-0lax"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="tg-0lax"></td>
                        <td class="tg-baqh" colspan="3" style="text-align: center;"><h1 style="color:#fff;">CERTIFICADO</h1></td>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <td class="tg-baqh" colspan="3" style="text-align: center;" rowspan="2"><p style="color:#fff;font-size: 22px;">Certificamos que <b><?= $model['name']?></b>, participou do Trote Solidário <?= $model['trote']?>, promovido pelo Núcleo Acadêmico Simers, <?= $model['frase_certificado']?></p></td>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <th class="tg-baqh" colspan="3" >
                            <img src="/var\www\html\trotesolidario\web\img\assinaturascertificado.jpg" style="width:auto;height: 80px;" alt="logo" />
                        </th>
                        <td class="tg-0lax"></td>
                    </tr>
                    <tr>
                        <td class="tg-0lax"></td>
                        <th class="tg-baqh" colspan="3" >
                            <img src="/var\www\html\trotesolidario\web\img\participacaocertificado.jpg" style="width:auto;height: 80px;" alt="logo" />
                        </th>
                        <td class="tg-0lax"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>  
</body>