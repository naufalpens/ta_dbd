<?php

namespace backend\controllers;

use Yii;
use backend\models\PrediksiKecamatan;
use backend\models\PrediksiKecamatanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\DbdNormal;
use backend\models\Dbd;
use backend\models\Kecamatan;

use backend\components\Helper;

/**
 * PrediksiKecamatanController implements the CRUD actions for PrediksiKecamatan model.
 */
class PrediksiKecamatanController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PrediksiKecamatan models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PrediksiKecamatanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PrediksiKecamatan model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PrediksiKecamatan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PrediksiKecamatan();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PrediksiKecamatan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing PrediksiKecamatan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PrediksiKecamatan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PrediksiKecamatan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PrediksiKecamatan::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionHitungKecamatan($metodeperhitungan, $metodebilacak) {
//        $tes = createKML();
        
        ini_set('max_execution_time', 30000);
        $time_start = microtime(true);
        
        $kecamatan = Kecamatan::find()->all();

        //Acak Solusi
        if($metodebilacak == 'biasa'){
            if($metodeperhitungan == 'linear'){
                $solusi = acakSolusi(5);
            } else {
                $solusi = acakSolusi(8);
            }
        } else if($metodebilacak == 'ratanol'){
            if($metodeperhitungan == 'linear'){
                $solusi = randomRatarataNol(5);
            } else {
                $solusi = randomRatarataNol(8);
            }
        } else{
            if($metodeperhitungan == 'linear'){
                $solusi = randomDistribusiNormal(5);
            } else {
                $solusi = randomDistribusiNormal(8);
            }
        }

        if($metodeperhitungan == 'linear'){
            for($id_kecamatan = 1; $id_kecamatan <= sizeof($kecamatan); $id_kecamatan++){
                $dbd = DbdNormal::findBySql("
                    SELECT *
                    FROM dbd_normal
                    WHERE tanggal NOT LIKE '%2012-%' AND id_kecamatan = ". $id_kecamatan ."
                ")->all();

                $dbd2 = DbdNormal::findBySql("
                    SELECT *
                    FROM dbd_normal
                    WHERE tanggal LIKE '%2012-%' AND id_kecamatan = ". $id_kecamatan ."
                ")->all();
                
                //Hitung Error
                $hitung = hitungLinear($solusi, $dbd);
                $error_tiap_data = $hitung['error_tiap_data'];
                $arr_error[] = sum($error_tiap_data) / sizeof($dbd);
                
                //Prediksi
                $hitung2 = hitungLinear($solusi, $dbd2);
                $error_tiap_data2 = $hitung2['error_tiap_data'];
                $arr_error2[] = sum($error_tiap_data2) / sizeof($dbd2);
            }
            $error = sum($arr_error) / sizeof($arr_error);
            $error2 = sum($arr_error2) / sizeof($arr_error2);
        } else {
            for($id_kecamatan = 1; $id_kecamatan <= sizeof($kecamatan); $id_kecamatan++){
                $dbd = DbdNormal::findBySql("
                    SELECT *
                    FROM dbd_normal
                    WHERE tanggal NOT LIKE '%2012-%' AND id_kecamatan = ". $id_kecamatan ."
                ")->all();

                $dbd2 = DbdNormal::findBySql("
                    SELECT *
                    FROM dbd_normal
                    WHERE tanggal LIKE '%2012-%' AND id_kecamatan = ". $id_kecamatan ."
                ")->all();
                
                //Hitung Error
                $hitung = hitungKuadratik($solusi, $dbd);
                $error_tiap_data = $hitung['error_tiap_data'];
                $arr_error[] = sum($error_tiap_data) / sizeof($dbd);
                
                //Prediksi
                $hitung2 = hitungKuadratik($solusi, $dbd2);
                $error_tiap_data2 = $hitung2['error_tiap_data'];
                $arr_error2[] = sum($error_tiap_data2) / sizeof($dbd2);
            }
            $error = sum($arr_error) / sizeof($arr_error);
            $error2 = sum($arr_error2) / sizeof($arr_error2);
        }

        //Penetapan Smin dan Emin
        $solusi_min = $solusi;
        $error_min = $error;

        //Prediksi
        $error_min2 = $error2;

        //Inisialisasi K dan T
        $K = $error_min * 10;
        $T = 0.1; $T0 = 0.1; $Tn = 0.0001;

        //Prediksi
        $K2 = $error_min2 * 10;

        //menghitung rata2 prediksi min
        $prediksi_min = sum($hitung['prediksi_tiap_data']) / sizeof($dbd);

        //menghitung rata2 Prediksi
        $prediksi_min2 = sum($hitung2['prediksi_tiap_data']) / sizeof($dbd2);

        $max_iterasi = 200;
        for ($iterasi = 0; $iterasi < $max_iterasi; $iterasi++){
            //Acak Solusi
            if($metodebilacak == 'biasa'){
                if($metodeperhitungan == 'linear'){
                    $solusi = acakSolusi(5);
                } else {
                    $solusi = acakSolusi(8);
                }
            } else if($metodebilacak == 'ratanol'){
                if($metodeperhitungan == 'linear'){
                    $solusi = randomRatarataNol(5);
                } else {
                    $solusi = randomRatarataNol(8);
                }
            } else{
                if($metodeperhitungan == 'linear'){
                    $solusi = randomDistribusiNormal(5);
                } else {
                    $solusi = randomDistribusiNormal(8);
                }
            }

            if($metodeperhitungan == 'linear'){
                for($id_kecamatan = 1; $id_kecamatan <= sizeof($kecamatan); $id_kecamatan++){
                    $dbd = DbdNormal::findBySql("
                        SELECT *
                        FROM dbd_normal
                        WHERE tanggal NOT LIKE '%2012-%' AND id_kecamatan = ". $id_kecamatan ."
                    ")->all();

                    $dbd2 = DbdNormal::findBySql("
                        SELECT *
                        FROM dbd_normal
                        WHERE tanggal LIKE '%2012-%' AND id_kecamatan = ". $id_kecamatan ."
                    ")->all();

                    //Hitung Error
                    $hitung = hitungLinear($solusi, $dbd);
                    $error_tiap_data = $hitung['error_tiap_data'];
                    $arr_error[] = sum($error_tiap_data) / sizeof($dbd);
                    $prediksi_tiap_data = $hitung['prediksi_tiap_data'];
                    $arr_prediksi[] = sum($prediksi_tiap_data) / sizeof($dbd);
                    $arr_kasus[] = sum($hitung['kasus']);

                    //Prediksi
                    $hitung2 = hitungLinear($solusi, $dbd2);
                    $error_tiap_data2 = $hitung2['error_tiap_data'];
                    $arr_error2[] = sum($error_tiap_data2) / sizeof($dbd2);
                    $prediksi_tiap_data2 = $hitung2['prediksi_tiap_data'];
                    $arr_prediksi2[] = sum($prediksi_tiap_data2) / sizeof($dbd2);
                    $arr_kasus2[] = sum($hitung2['kasus']);
                }
                $error = sum($arr_error) / sizeof($arr_error);
                $prediksi = sum($arr_prediksi) / sizeof($arr_prediksi);
                $kasus = sum($arr_kasus);
                
                $error2 = sum($arr_error2) / sizeof($arr_error2);
                $prediksi2 = sum($arr_prediksi2) / sizeof($arr_prediksi2);
                $kasus2 = sum($arr_kasus2);
            } else {
                for($id_kecamatan = 1; $id_kecamatan <= sizeof($kecamatan); $id_kecamatan++){
                    $dbd = DbdNormal::findBySql("
                        SELECT *
                        FROM dbd_normal
                        WHERE tanggal NOT LIKE '%2012-%' AND id_kecamatan = ". $id_kecamatan ."
                    ")->all();

                    $dbd2 = DbdNormal::findBySql("
                        SELECT *
                        FROM dbd_normal
                        WHERE tanggal LIKE '%2012-%' AND id_kecamatan = ". $id_kecamatan ."
                    ")->all();

                    //Hitung Error
                    $hitung = hitungKuadratik($solusi, $dbd);
                    $error_tiap_data = $hitung['error_tiap_data'];
                    $arr_error[] = sum($error_tiap_data) / sizeof($dbd);
                    $prediksi_tiap_data = $hitung['prediksi_tiap_data'];
                    $arr_prediksi[] = sum($prediksi_tiap_data) / sizeof($dbd);
                    $arr_kasus[] = sum($hitung['kasus']);

                    //Prediksi
                    $hitung2 = hitungKuadratik($solusi, $dbd2);
                    $error_tiap_data2 = $hitung2['error_tiap_data'];
                    $arr_error2[] = sum($error_tiap_data2) / sizeof($dbd2);
                    $prediksi_tiap_data2 = $hitung2['prediksi_tiap_data'];
                    $arr_prediksi2[] = sum($prediksi_tiap_data2) / sizeof($dbd2);
                    $arr_kasus2[] = sum($hitung2['kasus']);
                }
                $error = sum($arr_error) / sizeof($arr_error);
                $prediksi = sum($arr_prediksi) / sizeof($arr_prediksi);
                $kasus = sum($arr_kasus);
                
                $error2 = sum($arr_error2) / sizeof($arr_error2);
                $prediksi2 = sum($arr_prediksi2) / sizeof($arr_prediksi2);
                $kasus2 = sum($arr_kasus2);
            }

            //Pengubahan Smin dan Emin
            $r = (float)rand()/(float)getrandmax();
            $exp = exp( -($error - $error_min)/($K * $T));

            //Prediksi
            $exp2 = exp( -($error2 - $error_min2)/($K2 * $T));

            if($r < $exp){ //Simulated Annealing
            //if($error_min > $error){ //Montecarlo
                $solusi_min = $solusi;
                $error_min = $error;
                $prediksi_min = $prediksi;

                $error_min2 = $error2;
                $prediksi_min2 = $prediksi2;
            }

            //Pengubahan Temperature
            $coolingRate = 0.2;
            $T = $T0 * pow($Tn/$T0, $iterasi/$max_iterasi);

            //Pengisian data buat grafik
            if($iterasi%1 == 0){
                $params['error_min'][] = abs($error_min2);
            }
        }
        
        //Pengisian parameter di VIEW nya
        for($id_kecamatan = 1; $id_kecamatan <= sizeof($kecamatan); $id_kecamatan++){
            $dbd2 = DbdNormal::findBySql("
                SELECT *
                FROM dbd_normal
                WHERE tanggal LIKE '%2012-%' AND id_kecamatan = ". $id_kecamatan ."
            ")->all();
            
            $hitung2 = hitungLinear($solusi_min, $dbd2);
            $arr_kasus_tiap_kecamatan[] = sum($hitung2['kasus']);
            $arr_prediksi_tiap_kecamatan[] = sum($hitung2['prediksi_tiap_data']) / sizeof($dbd2);
            $arr_error_tiap_kecamatan[] = abs(sum($hitung2['kasus']) - (sum($hitung2['prediksi_tiap_data']) / sizeof($dbd2)));
        }
        Helper::vdump($solusi_min);
        Helper::vdump($arr_kasus_tiap_kecamatan);
        Helper::vdump($arr_prediksi_tiap_kecamatan);
        Helper::vdump($arr_error_tiap_kecamatan);
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        echo $time . '<br>';
        die("wes mandek");
        
        //return $this->render('hitung', ['params' => $params]);
        return $params;
    }
    
    public function actionGetPrediksi() {
        $jenisperhitungan = $_GET['jenisperhitungan'];
        $metodeperhitungan = $_GET['metodeperhitungan'];
        $metodebilacak = $_GET['metodebilacak'];
        
        if($jenisperhitungan == 'kecamatan'){
            $hitung = $this->actionHitungKecamatan($metodeperhitungan, $metodebilacak);
            return $this->render('hitung', ['params' => $hitung]);
        } else {
            $hitung = $this->actionHitungKecamatan($metodeperhitungan, $metodebilacak);
            return $this->render('hitung', ['params' => $hitung]);
        }
    }
}

function randomRatarataNol($banyak){
    srand(mktime());
    $average = 0;
    $solusi = [];
    $skala = 1;

    $ulang = true;

    while($ulang){
        for ($i = 0; $i < $banyak; $i++) {
            $random = ((float)rand()/(float)getrandmax() * ($skala * 2)) - $skala;
            $solusi[$i] = number_format($random, 3);
        }
        $average = average($solusi);

        if($average < 0.3 && $average > -0.3){
            $ulang = false;
        }
    }

    return $solusi;
}

function randomDistribusiNormal($banyak){    
    srand(mktime());
    $bilAcak = [];
    
    $a; $sw; $b=0; $c;
    for($i=0; $i<$banyak; $i++){
        $sw=0;
        while($sw == 0){
            $a = ((float)rand()/(float)getrandmax()) * 100;
            $b = 20 * $a / 100 - 10;
            $a = ((float)rand()/(float)getrandmax()) * 100;
            $c = $a / 100;

            if($c < exp(-$b * $b / 2 * pow(2,2)) / sqrt(6.28 * 8)){
               $sw = 1; 
            }
        }

        $bilAcak[$i] = number_format($b, 3);
    }
    
    return $bilAcak;
}

function acakSolusi ($banyak){
    srand(mktime());
    for ($i = 0; $i < $banyak; $i++) {
        $random = ((float)rand()/(float)getrandmax() * 20) - 10;
        $solusi[$i] = number_format($random, 3);
    }
//    $solusi = [0,0,0,0,0];
    return $solusi;
}

function hitungLinear($solusi, $dbd){    
    $total_kasus = 0;
    $error = 0;
    $hitungError['grandtotal_prediksi'] = 0;
    $hitungError['grandtotal_error'] = 0;
    
    foreach ($dbd as $k => $v) {
        $total_kasus += $dbd[$k]->kasus;
        $prediksi = ($solusi[0] * $dbd[$k]->ch) + ($solusi[1] * $dbd[$k]->hh) + ($solusi[2] * $dbd[$k]->abj) + ($solusi[3] * $dbd[$k]->hi) + ($solusi[4]);
        
        $pre[] = $prediksi;
        $error = abs($dbd[$k]->kasus - $prediksi);
        $error_prediksi[] = $error;
        
        $hitungError['ch'][] = $dbd[$k]->ch;
        $hitungError['hh'][] = $dbd[$k]->hh;
        $hitungError['abj'][] = $dbd[$k]->abj;
        $hitungError['hi'][] = $dbd[$k]->hi;
        $hitungError['kasus'][] = $dbd[$k]->kasus;
        $hitungError['prediksi_tiap_data'][] = number_format($prediksi, 2);
        $hitungError['error_tiap_data'][] = number_format($error, 2);
//        $hitungError['grandtotal_prediksi'] += $prediksi;
//        $hitungError['grandtotal_error'] += $error;
    }

//    $hitungError['rata_prediksi'] = number_format(average($pre), 2);
//    $hitungError['rata_error'] = number_format(average($error_prediksi), 2);
//    $hitungError['grandtotal_kasus'] = $total_kasus;

    return $hitungError;
}

function hitungKuadratik($solusi, $dbd){    
    $total_kasus = 0;
    $error = 0;
    $hitungError['grandtotal_prediksi'] = 0;
    $hitungError['grandtotal_error'] = 0;
    
    foreach ($dbd as $k => $v) {
        $total_kasus += $dbd[$k]->kasus;
        $prediksi = ($solusi[0] * pow($dbd[$k]->ch, 2)) + ($solusi[1] * $dbd[$k]->ch) + 
                ($solusi[2] * pow($dbd[$k]->hh, 2)) + ($solusi[3] * $dbd[$k]->hh) + 
                ($solusi[4] * pow($dbd[$k]->abj, 2)) + ($solusi[5] * $dbd[$k]->abj) +
                ($solusi[6] * pow($dbd[$k]->hi, 2)) + ($solusi[7] * $dbd[$k]->hi);
        
        $pre[] = $prediksi;
        $error = abs($dbd[$k]->kasus - $prediksi);
        $error_prediksi[] = $error;
        
        $hitungError['ch'][] = $dbd[$k]->ch;
        $hitungError['hh'][] = $dbd[$k]->hh;
        $hitungError['abj'][] = $dbd[$k]->abj;
        $hitungError['hi'][] = $dbd[$k]->hi;
        $hitungError['kasus'][] = $dbd[$k]->kasus;
        $hitungError['grandtotal_prediksi'] += $prediksi;
        $hitungError['grandtotal_error'] += $error;
        $hitungError['error_tiap_data'][] = number_format(abs($dbd[$k]->kasus - $prediksi), 2);
        $hitungError['prediksi_tiap_data'][] = number_format($prediksi, 2);
    }

    $hitungError['rata_prediksi'] = number_format(average($pre), 2);
    $hitungError['rata_error'] = number_format(average($error_prediksi), 2);
    $hitungError['grandtotal_kasus'] = $total_kasus;
//    $hitungError['grandtotal_prediksi'] = number_format(sum($hitungError['prediksi_tiap_data']), 2);
//    $hitungError['grandtotal_error'] = number_format(sum($hitungError['error_tiap_data']), 2);

    return $hitungError;
}
    
function average($data){
    $average = 0;
    foreach ($data as $v) {
        $average += $v;
    }
    $average = $average / sizeof($data);
    return $average;
}

function sum($data){
    $sum = 0;
    foreach ($data as $v) {
        $sum += $v;
    }    
    return $sum;
}

function createKML(){
    $kml = '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:schemaLocation="http://www.opengis.net/kml/2.2 http://schemas.opengis.net/kml/2.2.0/ogckml22.xsd http://www.google.com/kml/ext/2.2 http://code.google.com/apis/kml/schema/kml22gx.xsd">
<Document id="INDONESIA_KEC">
<name>INDONESIA_KEC</name>
<Snippet></Snippet>
<Folder id="FeatureLayer0">
<name>INDONESIA_KEC</name>
<Snippet></Snippet>

';
    
    $kecamatan = Kecamatan::find()->asArray()->all();
    foreach ($kecamatan as $k => $v){
        $kml .= '<Placemark>
<name>'. $v['nama_kecamatan'] .'</name>
<description>'.$v['nama_kecamatan'].'</description>
<LookAt>
<longitude>113.62262706</longitude>
<latitude>-8.07996199</latitude>
<range>27845</range>
</LookAt>
<styleUrl>#Line_Shape_ff0000_03</styleUrl>
<Polygon>
<outerBoundaryIs>
<LinearRing>
<coordinates>'.
$v['koordinat']
.'</coordinates>
</LinearRing>
';
        if($v['nama_kecamatan'] == 'Gumuk Mas'){
            $kml .= '</innerBoundaryIs>
</Polygon>
</Placemark>

';
        } else {
            $kml .= '</outerBoundaryIs>
</Polygon>
</Placemark>

';
        }
        
    }
    
    $kml .= '</Folder>
        
<Style id="PolyStylesedang">
<LabelStyle>
<color>00000000</color>
<scale>0.000000</scale>
</LabelStyle>
<LineStyle>
<color>ff666666</color>
<width>0.750000</width>
</LineStyle>
<PolyStyle>
<color>ff9dc6fe</color>
<outline>1</outline>
</PolyStyle>
</Style>

<Style id="PolyStylerendah">
<LabelStyle>
<color>00000000</color>
<scale>0.000000</scale>
</LabelStyle>
<LineStyle>
<color>ff666666</color>
<width>0.750000</width>
</LineStyle>
<PolyStyle>
<color>ffe4f1fd</color>
<outline>1</outline>
</PolyStyle>
</Style>

<Style id="PolyStyletinggi">
<LabelStyle>
<color>00000000</color>
<scale>0.000000</scale>
</LabelStyle>
<LineStyle>
<color>ff666666</color>
<width>0.750000</width>
</LineStyle>
<PolyStyle>
<color>ff7991c5</color>
<outline>1</outline>
</PolyStyle>
</Style>

</Document>
</kml>';
    
    file_put_contents("../kml/test.kml",$kml);
    die();
}