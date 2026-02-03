<?php

namespace app\modules\common\models;

use Yii;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\PessoaPonto;
use kartik\daterange\DateRangePicker;

class Helper {

    public static function getNamedRecords($json, $nome) {


        if (isset($json[$nome]['records'])) {
            $retorno = [];

            foreach ($json[$nome]['records'] as $record) {
                $arr = [];
                foreach ($record as $key => $value) {
                    $arr[$json[$nome]['columns'][$key]] = $value;
                }
                $retorno[] = $arr;
            }
            return $retorno;
        } else {
            return $json;
        }
    }

    public static function formatDate($data, $format = 'datetime') {
        $_data = null;

        if ($format == 'datetime') {
            if (($data) && ($data != '0000-00-00 00:00:00')) {
                $data = str_replace('/', '-', $data);
                $_data = \Yii::$app->formatter->asDateTime($data);
            }
        } elseif ($format == 'date') {
            if (($data) && ($data != '0000-00-00')) {
                $_data = \Yii::$app->formatter->asDate($data);
            }
        } elseif ($format == 'time') {
            if (($data) && ($data != '0000-00-00')) {
                list($_, $hora) = explode(' ', $data);
                $_data = $hora;
            }
        } elseif ($format == 'hora') {
            if (($data) && ($data != '0000-00-00')) {
                list($_, $hora) = explode(' ', $data);
                $_data = date('H:i:s', strtotime($hora));
            }
        }

        return $_data;
    }

    public static function formatDateDb($data, $format = 'datetime') {
        $_data = $data;

        if ($format == 'datetime') {
            //Helper::dump($data); // '02/07/2015 17:45'  2015-02-03 14:59:50 
            if (($data) && ($data != '0000-00-00 00:00:00')) {
                $arrData = explode('/', $data);
                $mes = $arrData[1];
                $dia = $arrData[0];
                $arrData2 = explode(' ', $arrData[2]);
                $ano = $arrData2[0];
                $hora = $arrData2[1] . ':00';

                $_data = $ano . '-' . $mes . '-' . $dia . ' ' . $hora;
            } else {
                $_data = null;
            }
        } elseif ($format == 'date') {
            if (($data) && ($data != '0000-00-00') && (strrpos($data, '/') != false )) {
                $arrData = explode('/', $data);

                $_data = $arrData[2] . '-' . $arrData[1] . '-' . $arrData[0];
            } else
                $_data = null;
        }

        return $_data;
    }

    public static function isDataAtrasada($data) {
        return (strtotime($data) < strtotime(self::getDataTimeNow()));
    }

    public static function verificaNumeroInteiro($numero = null) {
        if (isset($numero) && is_numeric($numero) && $numero > 0)
            return true;
        else
            throw new NotFoundHttpException('Número inválido');
        return false;
    }

    public static function asDecimal($num) {
        return \Yii::$app->formatter->asDecimal($num, 2);
    }

    public static function dump($var) {
        VarDumper::dump($var);
        die();
    }

    public static function d($var, $caller = null) {
        if (!isset($caller)) {
            $caller = debug_backtrace(1)[0];
        }
        echo '<code>File: ' . $caller['file'] . ' / Line: ' . $caller['line'] . '</code>';
        echo '<pre>';
        VarDumper::dump($var, 10, true);
        echo '</pre>';
        die();
    }

    public static function renderTitulo($str) {
        echo '<h3>' . Html::encode(ucfirst($str)) . '</h3>';
    }

    public static function getDataTimeNow() {
        return date('Y-m-d H:i:s');
    }

    public static function getDataNow() {
        return date('Y-m-d');
    }

    public static function getDataLastDay() {
        return date('Y-m-d', strtotime("-1 day", strtotime("now")));
    }

    public static function getMonth() {
        return date('m');
    }

    public static function getYear() {
        return date('Y');
    }

    public static function strSubstr($str, $length) {
        $result = $str . '';
        $result = str_replace('<br />', ' ', $result);
        if ($length < 5)
            $length = 5;
        if (strlen($str) > $length) {
            $result = substr($result, 0, ($length - 3)) . '...';
        }
        return $result;
    }

    public static function strProcuraEspacoeSubstr($str, $length) {
        $result = $str;
        $pos = strpos($str, ' ');
        if (($pos == false) or ($pos > $length)) {
            $result = self::strSubstr($str, $length);
        }
        return $result;
    }

    public static function verificaLinkHttp($str) {
        $result = $str;
        $pos = strpos($str, 'http');
        if (($pos !== 0)) {
            $result = 'http://' . $str;
        }
        return $result;
    }

    public static function tituloPaginaBreadcrumbs($breadcrumbs) {
        $result = '';
        if (isset($breadcrumbs[0])) {
            $result = $breadcrumbs[0];
            if (isset($breadcrumbs[1])) {
                $result = $breadcrumbs[1];
            }
            if (isset($breadcrumbs[2])) {
                $result = $breadcrumbs[2];
            }
        }
        return '<h2>' . $result . '</h2>';
    }

    public static function collapseClose($x = true) {
        $result = '<div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>';
        if ($x == true) {
            $result .= '<a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>';
        }
        $result .= '</div>';

        return $result;
    }

    public static function ultimoCadastroDias($dataUltimo) {
        if (!$dataUltimo)
            return '';
        $today = self::getDataTimeNow();
        $diff = strtotime($today) - strtotime($dataUltimo);
        $days = round(abs($diff / 60 / 60 / 24));
        return $days;
    }

    public static function getDiferencaDias($d1, $d2) {
        $diff = strtotime($d1) - strtotime($d2);
        $days = round(abs($diff / 60 / 60 / 24));
        return $days;
    }

    public static function getDiferencaDiasDot($d1, $d2) {
        $diff = strtotime($d1) - strtotime($d2);
        $days = round($diff / 60 / 60 / 24);
        return $days;
    }

    public static function getDiferencaMinutos($h1, $h2) {
        $diff = strtotime($h1) - strtotime($h2);
        $minutos = round(abs($diff / 60));
        return $minutos;
    }

    public static function layout_ad() {
        return (bool) (
                (Session::get('user.is_franqueado') == true) ||
                (Usuario::usuarioLogadoIsFranqueado())
                );
    }

    public static function getComboSimNao() {
        $tipo = 'Não definido';
        switch ($tipo) {
            case 1:
                $tipo = 'Sim';
                break;
            case 0:
                $tipo = 'Não';
                break;
        }
        return $tipo;
    }

    public static function verificaManutencao($force = false) {
        if ($force) {
            \Yii::$app->getResponse()->redirect(Url::to(['/site/manutencao_pagina/']), 200);
        }

        if ((isset(\Yii::$app->params['boolManutencao'])) && (\Yii::$app->params['boolManutencao'])) {
            \Yii::$app->getResponse()->redirect(Url::to(['/site/manutencao/']), 200);
        }
    }

    public static function validaUrlYoutube($url) {
        $result = true;
        if (empty($url))
        //$result['msg'] = 'Url vazia';
            $result = false;

        $posicao = strrpos($url, 'youtube');
        $posicao2 = strrpos($url, 'youtu.be');

        if ($posicao === false && $posicao2 === false) {
            //$result['msg'] = 'youtube na url não encontrada';
            $result = false;
        }

        return $result;
    }

    public static function getEmbedUrl($url) {
        $result = '';

        if (!self::validaUrlYoutube($url))
            return false;

        // Procura o que vem depois do caractere = na URL para pegar o código do vídeo
        $posicao = strrpos($url, '=');
        if ($posicao === false) {
            //$result['msg'] = 'Pos cod. da url not found';
            $posicao2 = strrpos($url, 'youtu.be/');
            if ($posicao2 === false) {
                return false;
            } else {
                $codigo = substr($url, (9 + $posicao2));
                $result = 'http://www.youtube.com/embed/' . $codigo;
            }
        } else {
            //$codigo = substr( $this->url, ++$posicao );
            //$result['url'] = 'http://www.youtube.com/embed/'. $codigo;            
            parse_str(parse_url($url, PHP_URL_QUERY), $arrUrl);
            $result = 'http://www.youtube.com/embed/' . $arrUrl['v'];
            //Helper::dump($arrUrl['v']);
        }

        return $result;
    }

    /*
      Recebe um dataRange do form kartik e transforma em um array com duas datas, a inicial e a final
      para facilitar a utilizacao em querys
     */

    public static function getDataRange($data) {
        $result = null;
        if (!empty($data) && strlen($data) == 23) { //'01/06/2015 - 30/11/2015'
            $d = explode(' - ', $data);
            $d1 = Helper::formatDateDb($d[0], 'date');
            $d2 = Helper::formatDateDb($d[1], 'date');
            //$query_data = "AND (data_prevista >= '$d1' AND data_prevista <= '$d2' ) ";
            $result = [$d1, $d2];
        } else if (strlen($data) == 10) {
            $result = [Helper::formatDateDb($data, 'date')];
        } else {
            throw new NotFoundHttpException('Data Range inválido');
        }
        return $result;
    }

    public static function retirarPerc($porcentagem) {
        return substr($porcentagem, 0, -1);
    }

    public static function retirarPerc_bd($porcentagem) {
        return str_replace(',', '.', substr($porcentagem, 0, -1));
    }

    public static function getDataRangeMesAtual() {
        return self::getDataRangePastMonths(0);
    }

    public static function getDataRangeMes() {
        return self::getDataRangePastMonths(1);
    }

    public static function getDataRangeTrismestre() {
        return self::getDataRangePastMonths(3);
    }

    public static function getDataRangeSemestre() {
        return self::getDataRangePastMonths(6);
    }

    public static function getDataRangeAno() {
        return self::getDataRangePastMonths(12);
    }

    public static function getDataRangeAnoAtual() {
        $data_inicio = date('01/01/Y');
        $data_fim = date('31/12/Y');
        $result = $data_inicio . ' - ' . $data_fim;

        return $result;
    }

    public static function getDataRangeDiaSQL() {

        $result = null;
        $data_i = date("Y-m-d H:i:s");
        $data_f = date("Y-m-d 23:59:59");

        $result = "'" . $data_i . "' and '" . $data_f . "'";

        return $result;
    }

    public static function getDataRangePastMonths($months) {
        $result = null;
        $data_inicio = date('01/m/Y');
        $data_fim = date('t/m/Y');
        if ($months) {
            if ($months != 1) {
                $data_inicio = date('01/m/Y', strtotime("-{$months} months"));
                $data_fim = date('t/m/Y', strtotime("-1 months"));
            } else {
                $data_inicio = date('01/m/Y', strtotime("-{$months} months"));
                $data_fim = date('t/m/Y', strtotime("-{$months} months"));
            }
        }
        $result = $data_inicio . ' - ' . $data_fim;

        return $result;
    }

    public static function getDataRangePorMes($month) {
        $result = null;
        //$d_hoje = date('Y-m-01'); //self::getDataNow();
        $d_hoje = date('01/m/Y'); //self::getDataNow();
        $d_hoje_i = date('Y-m-01'); //self::getDataNow();
        //$d_anterior = date('Y-m-d', strtotime("-$months months", strtotime($d_hoje)) );
        $d_anterior = date('d/m/Y', strtotime("-$month months", strtotime($d_hoje_i))); //Helper::dd($d_anterior);
        $month2 = $month - 1;
        $d_final = date('d/m/Y', strtotime("-$month2 months", strtotime($d_hoje_i))); //Helper::dd($d_anterior);
        //$d_anterior = date('d/m/Y', mktime(0, 0, 0, date("m") , date("d") - 30, date("Y")));Helper::dd($d_anterior);        

        $result = $d_anterior . ' - ' . $d_final;

        if ($month == 0) {
            $d_anterior = date('01/m/Y'); //Helper::dd($d_anterior); //self::getDataNow();
            $d_anterior_i = date('Y-m-01'); //Helper::dd($d_anterior); //self::getDataNow();
            $d_final = date('Y-m-d'); //self::getDataNow();
            //$d_anterior = date('Y-m-d', strtotime("-$months months", strtotime($d_hoje)) );
            $d_final = date('Y-m-d');
            //$d_hoje = date('d/m/Y');
            $d_final = date('t/m/Y', strtotime("+1 day", strtotime($d_hoje_i)));
        }
        $result = $d_anterior . ' - ' . $d_final;

        if (strlen($result) != 23)
            throw new NotFoundHttpException('Data Range inválido.');  //Helper::dd($result);
            
//Helper::dd($result); '01/07/2016 - 01/08/2016'
        return $result;
    }

    public static function getDataRangePorMesAno($month, $year) {
        $result = null;

        $d_hoje = date('01/' . $month . '/' . $year); //self::getDataNow();        
        $d_hoje_i = date($year . '-' . $month . '-01'); //self::getDataNow();

        $d_anterior = date('d/m/Y', strtotime($d_hoje_i)); //Helper::dd($d_anterior);                
        $d_final = date('d/m/Y', strtotime("+1 months", strtotime($d_hoje_i))); //Helper::dd($d_anterior);        

        $result = $d_anterior . ' - ' . $d_final;

        if ($month == 0) {
            $d_anterior = date('01/m/Y'); //Helper::dd($d_anterior); //self::getDataNow();
            $d_anterior_i = date('Y-m-01'); //Helper::dd($d_anterior); //self::getDataNow();
            $d_final = date('Y-m-d'); //self::getDataNow();
            //$d_anterior = date('Y-m-d', strtotime("-$months months", strtotime($d_hoje)) );
            $d_final = date('Y-m-d');
            //$d_hoje = date('d/m/Y');
            $d_final = date('t/m/Y', strtotime("+1 day", strtotime($d_hoje_i)));
        }
        $result = $d_anterior . ' - ' . $d_final;

        if (strlen($result) != 23)
            throw new NotFoundHttpException('Data Range inválido.');  //Helper::dd($result);
            
//Helper::dd($result); '01/07/2016 - 01/08/2016'
        return $result;
    }

    public static function getArrayUltimoMesEAtual() {

        $result = [];
        $arr = [];

        $d_hoje_i = date('Y-m-01');

        $arr = [];
        $arr['mes'] = date('m', strtotime("-1 month", strtotime($d_hoje_i)));
        $arr['ano'] = date('Y', strtotime("-1 month", strtotime($d_hoje_i)));
        array_push($result, $arr);
        $arr = [];
        $arr['mes'] = date('m');
        $arr['ano'] = date('Y');
        array_push($result, $arr);

        return $result;
    }

    /**
     * Retorna uma string com o nome do mês.
     * 
     * @param integer $month
     * @return string
     */
    public static function getDateMonthString($month, $substr = false) {
        switch ($month) {
            case 1:
                $result = 'Janeiro';
                break;
            case 2:
                $result = 'Fevereiro';
                break;
            case 3:
                $result = 'Março';
                break;
            case 4:
                $result = 'Abril';
                break;
            case 5:
                $result = 'Maio';
                break;
            case 6:
                $result = 'Junho';
                break;
            case 7:
                $result = 'Julho';
                break;
            case 8:
                $result = 'Agosto';
                break;
            case 9:
                $result = 'Setembro';
                break;
            case 10:
                $result = 'Outubro';
                break;
            case 11:
                $result = 'Novembro';
                break;
            case 12:
                $result = 'Dezembro';
                break;
            default:
                $result = '';
                break;
        }

        if ($substr) {
            $result = substr($result, 0, 3);
        }

        return $result;
    }

    /**
     * Returna true se o usuário estiver acessando o portal com um smarthphone ou tablet.
     * 
     * @return boolean
     */
    public static function isMobile() {
        $mobile = false;
        $iphone = strpos($_SERVER['HTTP_USER_AGENT'], "iPhone");
        $ipad = strpos($_SERVER['HTTP_USER_AGENT'], "iPad");
        $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
        $palmpre = strpos($_SERVER['HTTP_USER_AGENT'], "webOS");
        $berry = strpos($_SERVER['HTTP_USER_AGENT'], "BlackBerry");
        $ipod = strpos($_SERVER['HTTP_USER_AGENT'], "iPod");
        $symbian = strpos($_SERVER['HTTP_USER_AGENT'], "Symbian");

        if ($iphone || $ipad || $android || $palmpre || $ipod || $berry || $symbian == true) {
            $mobile = true;
        }
        return $mobile;
    }

    public static function print_valor($v) {
        echo ($v > 0 ) ? \Yii::$app->formatter->asCurrency($v, 'R$ ') : '0';
    }

    /**
     * Mantem o elemento/texto na mesma linha sem fazer a quebra dela para a próxima linha
     * 
     * @param string $string
     * @return string
     */
    public static function TextSameLine($string) {
        return "<span style='white-space: nowrap;'>{$string}</span>";
    }

    public static function getRangeOfDataRange() {
        $primeiro_mes = self::getDateMonthString(date('m', strtotime('-1 month'))) . '/' . date('Y', strtotime('-1 month'));
        $segundo_mes = self::getDateMonthString(date('m', strtotime('-2 month'))) . '/' . date('Y', strtotime('-2 month'));
        $terceiro_mes = self::getDateMonthString(date('m', strtotime('-3 month'))) . '/' . date('Y', strtotime('-3 month'));
        $quarto_mes = self::getDateMonthString(date('m', strtotime('-4 month'))) . '/' . date('Y', strtotime('-4 month'));

        $range = [
            'Este Mês' => ["moment().startOf('month')", "moment().endOf('month')"],
            $primeiro_mes => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
            $segundo_mes => ["moment().subtract(2, 'month').startOf('month')", "moment().subtract(2, 'month').endOf('month')"],
            $terceiro_mes => ["moment().subtract(3, 'month').startOf('month')", "moment().subtract(3, 'month').endOf('month')"],
            $quarto_mes => ["moment().subtract(4, 'month').startOf('month')", "moment().subtract(4, 'month').endOf('month')"],
        ];

        return $range;
    }

    /**
     * Função que recebe o uma string com nome do estado ou a sigla e retorna um integer que é o ID desse estado.
     * 
     */
    public static function getEstadoId($estado) {
        $tamanho_string = strlen($estado);
        $Estado_id = 25; // ID padrão para quando não tem ou não acha o estado

        if ($tamanho_string > 0) {
            if ($tamanho_string == 2) {
                switch ($estado) {
                    case 'AC': $Estado_id = 1;
                        break;
                    case 'AL': $Estado_id = 2;
                        break;
                    case 'AP': $Estado_id = 3;
                        break;
                    case 'AM': $Estado_id = 4;
                        break;
                    case 'BA': $Estado_id = 5;
                        break;
                    case 'CE': $Estado_id = 6;
                        break;
                    case 'DF': $Estado_id = 7;
                        break;
                    case 'ES': $Estado_id = 8;
                        break;
                    case 'GO': $Estado_id = 9;
                        break;
                    case 'MA': $Estado_id = 10;
                        break;
                    case 'MT': $Estado_id = 11;
                        break;
                    case 'MS': $Estado_id = 12;
                        break;
                    case 'MG': $Estado_id = 13;
                        break;
                    case 'PA': $Estado_id = 14;
                        break;
                    case 'PR': $Estado_id = 15;
                        break;
                    case 'PB': $Estado_id = 16;
                        break;
                    case 'PE': $Estado_id = 17;
                        break;
                    case 'PI': $Estado_id = 18;
                        break;
                    case 'RJ': $Estado_id = 19;
                        break;
                    case 'RN': $Estado_id = 20;
                        break;
                    case 'RS': $Estado_id = 21;
                        break;
                    case 'RO': $Estado_id = 22;
                        break;
                    case 'RR': $Estado_id = 23;
                        break;
                    case 'SC': $Estado_id = 24;
                        break;
                    case 'SP': $Estado_id = 25;
                        break;
                    case 'SE': $Estado_id = 26;
                        break;
                    case 'TO': $Estado_id = 27;
                        break;
                }
            } else {
                $estado = strtolower($estado);
                switch ($estado) {
                    case 'acre': $Estado_id = 1;
                        break;
                    case 'alagoas': $Estado_id = 2;
                        break;
                    case 'amapá': $Estado_id = 3;
                        break;
                    case 'amazonas': $Estado_id = 4;
                        break;
                    case 'bahia': $Estado_id = 5;
                        break;
                    case 'ceará': $Estado_id = 6;
                        break;
                    case 'distrito federal': $Estado_id = 7;
                        break;
                    case 'espirito santo': $Estado_id = 8;
                        break;
                    case 'goiás': $Estado_id = 9;
                        break;
                    case 'maranhão': $Estado_id = 10;
                        break;
                    case 'mato grosso': $Estado_id = 11;
                        break;
                    case 'mato grosso do sul': $Estado_id = 12;
                        break;
                    case 'Minas gerais': $Estado_id = 13;
                        break;
                    case 'pará': $Estado_id = 14;
                        break;
                    case 'paraná': $Estado_id = 15;
                        break;
                    case 'paraíba': $Estado_id = 16;
                        break;
                    case 'pernambuco': $Estado_id = 17;
                        break;
                    case 'piauí': $Estado_id = 18;
                        break;
                    case 'rio de janeiro': $Estado_id = 19;
                        break;
                    case 'rio grande do norte': $Estado_id = 20;
                        break;
                    case 'rio grande do sul': $Estado_id = 21;
                        break;
                    case 'rondônia': $Estado_id = 22;
                        break;
                    case 'roraima': $Estado_id = 23;
                        break;
                    case 'santa catarina': $Estado_id = 24;
                        break;
                    case 'são paulo': $Estado_id = 25;
                        break;
                    case 'sergipe': $Estado_id = 26;
                        break;
                    case 'tocantins': $Estado_id = 27;
                        break;
                }
            }
        }
        return $Estado_id;
    }

    /**
     * Retorna um array com os nomes dos meses.
     *
     * @param integer $month
     * @return string
     */
    public static function getSelectMonth() {
        $mes = [0 => '',
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'];

        return $mes;
    }

    public function prim_maiuscula($str) {
        return ucfirst(strtolower($str));
    }

    /**
     *  Retorna um array de semanas com a data de inicio e data de fim da semana.
     *  
     * @param string $data_ini
     * @param string $data_ini
     * 
     * @return array 
     */
    public static function separarDataPorSemanas($data_ini, $data_fim) {
        $data_obj_ini = new \DateTime($data_ini);
        $data_obj_fim = new \DateTime($data_fim);
        $diferenca_dias = $data_obj_ini->diff($data_obj_fim);
        $contar_dias = $diferenca_dias->days + 1;

        $dia_ini_string = date('D', strtotime($data_ini));
        switch ($dia_ini_string) {
            case 'Sun':
                $calculo_dia = 7;
                break;
            case 'Mon':
                $calculo_dia = 6;
                break;
            case 'Tue':
                $calculo_dia = 5;
                break;
            case 'Wed':
                $calculo_dia = 4;
                break;
            case 'Thu':
                $calculo_dia = 3;
                break;
            case 'Fri':
                $calculo_dia = 2;
                break;
            case 'Sat':
                $calculo_dia = 1;
                break;
        }

        $count_sem1 = ($contar_dias <= $calculo_dia) ? $contar_dias : $calculo_dia;
        $count_sem2 = 0;
        $count_sem3 = 0;
        $count_sem4 = 0;
        $count_sem5 = 0;

        for ($i = 0; $i < ($contar_dias - $count_sem1); $i++) {
            if ($count_sem4 == 7 && $count_sem5 < 7) {
                $count_sem5++;
            }
            if ($count_sem3 == 7 && $count_sem4 < 7) {
                $count_sem4++;
            }
            if ($count_sem2 == 7 && $count_sem3 < 7) {
                $count_sem3++;
            }
            if (($count_sem2 < 7)) {
                $count_sem2++;
            }
        }

        $data_semanas = [];

        //montando primeira semana
        $sem1_ini = date('Y-m-d', strtotime("{$data_ini}"));
        $sem1_fim = date('Y-m-d', strtotime("{$sem1_ini} + " . ($count_sem1 - 1) . " Days"));

        $data_semanas['semana1'] = [$sem1_ini, $sem1_fim];

        //montando segunda semana
        if ($count_sem2 > 0) {
            $sem2_ini = date('Y-m-d', strtotime("{$sem1_fim} + 1 Day"));
            $sem2_fim = date('Y-m-d', strtotime("{$sem2_ini } + " . ($count_sem2 - 1) . " Days"));
            $data_semanas['semana2'] = [$sem2_ini, $sem2_fim];
        }

        //montando terceira semana
        if ($count_sem3 > 0) {
            $sem3_ini = date('Y-m-d', strtotime("{$sem2_fim} + 1 Day"));
            $sem3_fim = date('Y-m-d', strtotime("{$sem3_ini } + " . ($count_sem3 - 1) . " Days"));
            $data_semanas['semana3'] = [$sem3_ini, $sem3_fim];
        }

        //montando segunda semana
        if ($count_sem4 > 0) {
            $sem4_ini = date('Y-m-d', strtotime("{$sem3_fim} + 1 Day"));
            $sem4_fim = date('Y-m-d', strtotime("{$sem4_ini } + " . ($count_sem4 - 1) . " Days"));
            $data_semanas['semana4'] = [$sem4_ini, $sem4_fim];
        }

        //montando segunda semana
        if ($count_sem5 > 0) {
            $sem5_ini = date('Y-m-d', strtotime("{$sem4_fim} + 1 Day"));
            $sem5_fim = date('Y-m-d', strtotime("{$sem5_ini } + " . ($count_sem5 - 1) . " Days"));
            $data_semanas['semana5'] = [$sem5_ini, $sem5_fim];
        }

        return $data_semanas;
    }

    public function getPorcentagemProgressaoData($data_inicial, $data_final) {
        $result = 0;
        $data_hoje = self::getDataNow();

        $diferenca_1 = (int) self::getDiferencaDias($data_inicial, $data_hoje);
        $diferenca_2 = (int) self::getDiferencaDias($data_inicial, $data_final);
        if ($diferenca_2 != 0) {
            $result = (int) ($diferenca_1 * 100 / $diferenca_2);
        }

        if ($result > 100)
            $result = 100;
        if ($result < 1)
            $result = 1;

        return $result;
    }

    public static function redirectBeforeAction() {
        $usuario = Usuario::getUsuarioLogado();
        if (isset($usuario)) {
            if ($usuario->isFranqueado() && !$usuario->usuarioLogadoFezTreinamentoPresencial()) {
                return \Yii::$app->controller->redirect(['intreinamento/pre_treinamento', 'flash' => true]);
            }
        }
    }

    public static function feriados($ano) {

        $dia = 86400;
        $datas = array();
        $datas['pascoa'] = easter_date($ano);
        $datas['sexta_santa'] = $datas['pascoa'] - (2 * $dia);
        $datas['carnaval'] = $datas['pascoa'] - (47 * $dia);
        $datas['corpus_cristi'] = $datas['pascoa'] + (60 * $dia);
        $feriados = array(
            $ano . '-01-01', // Confraternização Universal
            $ano . '-02-02', // Navegantes
            date('Y-m-d', $datas['carnaval']),
            date('Y-m-d', $datas['sexta_santa']),
            date('Y-m-d', $datas['pascoa']),
            $ano . '-04-21', // Tiradentes
            $ano . '-05-01', // Dia do trabalhador
            date('Y-m-d', $datas['corpus_cristi']),
            $ano . '-09-07', // Independência do Brasil
            $ano . '-09-20', // Revolução Farroupilha \m/
            $ano . '-10-12', // Nossa Sra Aparecida
            $ano . '-11-02', // Finados
            $ano . '-11-15', // Proclamação da República
            $ano . '-12-25', // Natal
        );
        return $feriados;
    }

    public static function dataToTimestamp($data) {

        $ano = substr($data, 6, 4);
        $mes = substr($data, 3, 2);
        $dia = substr($data, 0, 2);

        return mktime(0, 0, 0, $mes, $dia, $ano);
    }

    function soma1dia($data) {

        $ano = substr($data, 6, 4);
        $mes = substr($data, 3, 2);
        $dia = substr($data, 0, 2);

        return date("d/m/Y", mktime(0, 0, 0, $mes, $dia + 1, $ano));
    }

    public static function getDiferencaDiasUteis($DataInicial, $DataFinal) {

        $diaFDS = 0; //dias não úteis(Sábado=6 Domingo=0)
        $calculoDias = self::getDiferencaDias($DataFinal, $DataInicial); //número de dias entre a data inicial e a final
        $diasUteis = 0;
        $yDataInicial = self::formatDate($DataInicial, 'date');
        $yDataFinal = self::formatDate($DataFinal, 'date');

        if ($DataInicial < $DataFinal) {
            while ($yDataInicial != $yDataFinal) {
                $diaSemana = date("w", self::dataToTimestamp($yDataInicial));
                if ($diaSemana == 0 || $diaSemana == 6) { //se SABADO OU DOMINGO, SOMA 01 ao FDS              
                    $diaFDS++;
                } else { //Se não verifica se este dia é FERIADO
                    for ($i = 0; $i < 15; $i++) {
                        if ($yDataInicial == self::feriados(date("Y"), $i)) {
                            $diaFDS++;
                        }
                    }
                }
                $yDataInicial = self::soma1dia($yDataInicial); //dia + 1
            }
        } else {
            while ($yDataFinal != $yDataInicial) {

                $diaSemana = date("w", self::dataToTimestamp($yDataFinal));
                if ($diaSemana == 0 || $diaSemana == 6) { //se SABADO OU DOMINGO, SOMA 01 ao FDS              
                    $diaFDS++;
                } else { //Se não verifica se este dia é FERIADO
                    for ($i = 0; $i < 15; $i++) {
                        if ($yDataFinal == self::feriados(date("Y"), $i)) {
                            $diaFDS++;
                        }
                    }
                }
                $yDataFinal = self::soma1dia($yDataFinal); //dia + 1
            }
        }
        return ($calculoDias > 0) ? $calculoDias - $diaFDS : $calculoDias + $diaFDS;
    }

    public static function getUltimoDiaMes($mes) {
        return date("t", mktime(0, 0, 0, $mes, '01', date("Y")));
    }

    public static function getLastDateMes($mes) {
        return date("Y-" . $mes . "-" . self::getUltimoDiaMes($mes) . " 23:59:59");
    }

    public static function getLastDateYear($year) {
        return date($year . "-12-" . self::getUltimoDiaMes("12") . " 23:59:59");
    }

    public static function getFirstDateMes($mes) {
        return date("Y-" . $mes . "-01 00:00:00");
    }

    public static function getFirstDateYear($year) {
        return date($year . "-01-01 00:00:00");
    }

    public static function getDataIsMesAtual($data) {
        return (strtotime($data) >= strtotime(self::getFirstDateMes(date('m'))) && strtotime($data) <= strtotime(self::getLastDateMes(date('m')))) ? true : false;
    }

    public static function getDataIsProximoMes($data) {
        $proximo_mes = date('m', strtotime('+1 months', strtotime(date('Y-m-d'))));
        return (strtotime($data) >= strtotime(self::getFirstDateMes(date('m'))) && strtotime($data) <= strtotime(self::getLastDateMes($proximo_mes))) ? true : false;
    }

    public static function getPrimeiroDiaAno() {
        return date('Y-01-01');
    }

    public function getAnoPassado() {
        return date('Y', strtotime('-1 years', strtotime(date('Y-m-d'))));
    }

    public function getProximoAno() {
        return date('Y', strtotime('+1 years', strtotime(date('Y-m-d'))));
    }

    public static function getClasseProgressBar($porcentagem) {
        if ($porcentagem < 50) {
            return 'progress-bar progress-bar-danger';
        } else if ($porcentagem >= 50 && $porcentagem < 100) {
            return 'progress-bar progress-bar-warning';
        } else {
            return 'progress-bar';
        }
    }

    public static function retirar_ponto_virgula_int($string) {
        $result = str_replace('.', '', $string);
        $result = str_replace(',', '', $result);
        if ($result == '')
            $result = null;
        return $result;
    }

    public static function getTextoMes($mes) {
        switch ($mes) {
            case '01':
                return "Janeiro";
            case '02':
                return "Fevereiro";
            case '03':
                return "Março";
            case '04':
                return "Abril";
            case '05':
                return "Maio";
            case '06':
                return "Junho";
            case '07':
                return "Julho";
            case '08':
                return "Agosto";
            case '09':
                return "Setembro";
            case '10':
                return "Outubro";
            case '11':
                return "Novembro";
            case '12':
                return "Dezembro";
            default:
                return "Mês Inválido";
        }
    }

    public static function formataCNPJ($cnpj) {

        if (strlen($cnpj) == 14) {

            $aux1 = substr($cnpj, 0, 2);
            $aux2 = substr($cnpj, 2, 3);
            $aux3 = substr($cnpj, 5, 3);
            $aux4 = substr($cnpj, 8, 4);
            $aux5 = substr($cnpj, 12, 2);

            $cnpj = $aux1 . '.' . $aux2 . '.' . $aux3 . '/' . $aux4 . '-' . $aux5;
        }

        return $cnpj;
    }

    public static function get_cnae_receita_by_cnpj($cnpj_limpo) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $cnpj = $cnpj_limpo;

        $json = @file_get_contents('https://www.receitaws.com.br/v1/cnpj/' . $cnpj);
        $resultado = json_decode($json, true);

        return $resultado;
    }

    public static function get_micro_business_titulo() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $json = @file_get_contents('http://192.168.2.38/api/api.php/MICRO_BUSINESS_TITULOS');
        $resultado = json_decode($json, true);

        $codigo = [];

        foreach ($resultado['MICRO_BUSINESS_TITULOS']['records'] as $value) {
            if ($value[5] == 'S') {
                array_push($codigo, [$value[0], $value[1], $value[3], $value[5]]);
            }
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;

        return $codigo;
    }

    public static function getMoneyFormat($valor) {
        if (!$valor) {
            return 0;
        } else {
            return number_format($valor, 2, ',', '.');
        }
    }

    public static function conferir_ip_externo_interno() {
        $ip = ['127.0.0.1', '::1', 'localhost', '192.168.3.15', '192.168.3.60'];
        if (!(in_array($_SERVER['REMOTE_ADDR'], $ip))) {
            return false;
        }
    }

    public static function get_combo_data_range() {

        //dd(self::verifica_trimestres());
        $data_valor = '';

        if (Yii::$app->request->get()) {

            $post = Yii::$app->request->get();

            if (isset($post['data'])) {

                $data_valor = $post['data'];
            }
        }
        if ($data_valor) {
            $data_valor = date('01/m/y') . " - " . date('t/m/y');
        }

        $data = DateRangePicker::widget([
                    'name' => 'data',
                    'useWithAddon' => false,
                    'hideInput' => true,
                    'value' => $data_valor,
                    'options' => ['id' => 'combo-data'],
                    'pluginOptions' => [
                        'language' => 'pt',
                        'locale' => [
                            'format' => 'DD/MM/YYYY'
                        ],
                        'format' => 'DD/MM/YYYY',
                        'ranges' => [
                            'Este Mês' => ["moment().startOf('month')", "moment().endOf('month')"],
                            'Último mês' => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                            'Últimos ano' => ["moment().subtract(1, 'year').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                        ],
                    ],
        ]);


        return $data;
    }

    public static function getDiasUteis($DataInicial, $DataFinal) {

        $data_ini = new \DateTime($DataInicial);
        $data_fim = new \DateTime($DataFinal);
        $data_dif = $data_ini->diff($data_fim);
        $dias = [];
        $dia_inicio = strtotime($DataInicial);
        $dia = 86400;
        $ano = date('Y');


        for ($i = 0; $i <= $data_dif->days; $i++) {
            if (date('w', $dia_inicio) > 0 && date('w', $dia_inicio) < 6 && !in_array(date('Y-m-d', $dia_inicio), self::feriados($ano))) {

                array_push($dias, date('Y-m-d', $dia_inicio));
            }
            $dia_inicio += $dia;
        }

        return $dias;
    }

    public static function getDiasUteisTrabalhados($DataInicial, $DataFinal, $id) {

        $data_ini = new \DateTime($DataInicial);
        $data_fim = new \DateTime($DataFinal);
        $data_dif = $data_ini->diff($data_fim);
        $dias = [];
        $dia_inicio = strtotime($DataInicial);
        $dia = 86400;
        $ano = date('Y');

        for ($i = 1; $i <= $data_dif->days; $i++) {
            if (date('w', $dia_inicio) > 0 && date('w', $dia_inicio) < 6 && !in_array(date('Y-m-d', $dia_inicio), self::feriados($ano))) {
                $ponto = PessoaPonto::find()->where(['pessoa_id' => $id, 'data' => date('Y-m-d', $dia_inicio)])->one();

                array_push($dias, date('Y-m-d', $dia_inicio));
            }
            $dia_inicio += $dia;
        }


        return $dias;
    }

    public static function converterHora($total_segundos) {

        $hora = sprintf("%02s", floor($total_segundos / (60 * 60)));
        $total_segundos = ($total_segundos % (60 * 60));

        $minuto = sprintf("%02s", floor($total_segundos / 60));
        $total_segundos = ($total_segundos % 60);

        $hora_minuto = $hora . ":" . $minuto;
        return $hora_minuto;
    }

    public static function calculaDifHora($hora1, $hora2) {

        $entrada = $hora1;
        $saida = $hora2;
        $hora1 = explode(":", $entrada);
        $hora2 = explode(":", $saida);
        $acumulador1 = ($hora1[0] * 3600) + ($hora1[1] * 60) + $hora1[2];
        $acumulador2 = ($hora2[0] * 3600) + ($hora2[1] * 60) + $hora2[2];
        $resultado = $acumulador2 - $acumulador1;
        $hora_ponto = floor($resultado / 3600);
        $resultado = $resultado - ($hora_ponto * 3600);
        $min_ponto = floor($resultado / 60);
        $resultado = $resultado - ($min_ponto * 60);
        $secs_ponto = '00';
        //Grava na variável resultado final
        $saldo = ($hora_ponto < 0) ? 'neg' : 'pos';
        $tempo = $hora_ponto . ":" . $min_ponto . ":" . $secs_ponto;
        $tempo = [$tempo, $saldo];
        return $tempo;
    }
    
    public static function webservice($rota,$argumentos){
        $argumentos += ['token' => '4396d8be6d916ddf44c2b781487fca2f5b4b430408f0dc047076196ef43c465e'];
        
        $request = curl_init();
        //curl_setopt($request, CURLOPT_URL, 'http://10.0.0.16:4040/'.$rota);
        curl_setopt($request, CURLOPT_URL, 'http://webservice.simers.org.br/'.$rota);
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($request, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));
        $args = json_encode($argumentos);
        curl_setopt($request, CURLOPT_POSTFIELDS, $args);
        $result = curl_exec($request);
        curl_close($request);
        return json_decode($result,true);
    }

    public static function webserviceLocation() {
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if ($ip == "::1" || $ip=="172.17.0.1") {
            $ip = '187.39.119.148';
        }
        $url = 'https://api.ipgeolocation.io/ipgeo?apiKey=245e00dec5e14a3ba6629aea185c7bcd&ip=' . $ip . '&output=xml';
        $arr = '';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $sXML = curl_exec($ch);
        curl_close($ch);

        if ($sXML) {
            $xml = simplexml_load_string($sXML);
            $json = json_encode($xml);
            $arr = json_decode($json, true);
        }

        return $arr;
    }
    
    public static function Navegador() {
        $MSIE = strpos($_SERVER['HTTP_USER_AGENT'], "MSIE");
        $MSIE2 = strpos($_SERVER['HTTP_USER_AGENT'], "Trident");
        $Firefox = strpos($_SERVER['HTTP_USER_AGENT'], "Firefox");
        $Mozilla = strpos($_SERVER['HTTP_USER_AGENT'], "Mozilla");
        $Chrome = strpos($_SERVER['HTTP_USER_AGENT'], "Chrome");
        $Chromium = strpos($_SERVER['HTTP_USER_AGENT'], "Chromium");
        $Safari = strpos($_SERVER['HTTP_USER_AGENT'], "Safari");
        $Opera = strpos($_SERVER['HTTP_USER_AGENT'], "Opera");

        if ($MSIE == true) {
            $navegador = "IE";
        }
        if ($MSIE2 == true) {
            $navegador = "IE";
        } else if ($Firefox == true) {
            $navegador = "Firefox";
        } else if ($Mozilla == true) {
            $navegador = "Firefox";
        } else if ($Chrome == true) {
            $navegador = "Chrome";
        } else if ($Chromium == true) {
            $navegador = "Chromium";
        } else if ($Safari == true) {
            $navegador = "Safari";
        } else if ($Opera == true) {
            $navegador = "Opera";
        } else {
            $navegador = $_SERVER['HTTP_USER_AGENT'];
        }

        return $navegador;
    }

    /**
     * Determine the mobile operating system.
     * This function returns one of 'iOS', 'Android', 'Windows Phone', or 'unknown'.
     *
     * @returns {String}
     */
    public static function getMobileOperatingSystem() {
        $mobile = FALSE;
        $user_agents = array("iPhone", "iPad", "Android", "webOS", "BlackBerry", "iPod", "Symbian", "IsGeneric");

        foreach ($user_agents as $user_agent) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], $user_agent) !== FALSE) {
                $mobile = TRUE;
                $modelo = $user_agent;
                break;
            }
        }

        if ($mobile) {
            return strtolower($modelo);
        } else {
            return "computador";
        }
    }
}
