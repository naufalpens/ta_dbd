<?php

namespace backend\controllers;

use Yii;
use backend\models\PrediksiKecamatan;
use backend\models\PrediksiKecamatanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\DbdNormal;
use backend\models\Kecamatan;

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
    
    public function actionHitungLinear() {
//        $tes = createKML();
//        die();

        $x=1;
        ini_set('max_execution_time', 30000);
        $time_start = microtime(true);
        
        $id_kecamatan = 1;

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
        
        //Acak Solusi
        //$solusi = acakSolusi(5);
        //$solusi = randomDistribusiNormal(5);
        $solusi = randomRatarataNol(5);
        
        //Hitung Error        
        $hitung = hitungError($solusi, $dbd);
        $error = $hitung['error'];
        
        //Prediksi
        $hitung2 = hitungError($solusi, $dbd2);
        $error2 = $hitung2['error'];        
        
        //Penetapan Smin dan Emin
        $solusi_min = $solusi;
        $error_min = $error;
        
        //Prediksi
        $error_min2 = $error2;
        
        //Inisialisasi K dan T
        $K = $error_min * 10;
        $T = 0.8;
        
        //Prediksi
        $K2 = $error_min2 * 10;
        
        //prediksi min
        $prediksi_min = $hitung['prediksi'];
        
        //Prediksi
        $prediksi_min2 = $hitung2['prediksi'];
        
        for ($iterasi = 0; $iterasi < 10000; $iterasi++){
            //Acak Solusi
            //$solusi = acakSolusi(5);
            //$solusi = randomDistribusiNormal(5);
            $solusi = randomRatarataNol(5);

            //Hitung Error
            $hitung = hitungError($solusi, $dbd);
            $error = $hitung['error'];
            $prediksi = $hitung['prediksi'];
            
            //Prediksi
            $hitung2 = hitungError($solusi, $dbd2);
            $error2 = $hitung2['error'];
            $prediksi2 = $hitung2['prediksi'];

            //Pengubahan Smin dan Emin
            $r = (float)rand()/(float)getrandmax();
            $exp = exp( -($error - $error_min)/($K * $T));
            
            //Prediksi
            $exp2 = exp( -($error2 - $error_min2)/($K2 * $T));
            
            //if($r < $exp){ //Simulated Annealing
            if($error_min > $error){ //Montecarlo
                $solusi_min = $solusi;
                $error_min = $error;
                $prediksi_min = $prediksi;
                
                $error_min2 = $error2;
                $prediksi_min2 = $prediksi2;
                //echo $x++ . "<br>";
            }
            
            //Pengubahan Temperature
            $coolingRate = 0.2;
            $T = $T * (1 - $coolingRate);

            //Pengisian Params untuk ditampilkan ke View
            $params['solusi_min'] = $solusi_min;
            $params['kasus'] = $hitung2['total_kasus'];
            if(isset($prediksi_min2)){
                $params['prediksi_min'] = abs($prediksi_min2);
            } else{
                $params['prediksi_min'] = abs($prediksi2);
                var_dump($iterasi ." : ". $prediksi2); die();
            }
            $params['t_akhir'] = $T;
            if($iterasi%100 == 0){
                $params['error_min'][] = $error_min2;
            }
        }
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        
        return $this->render('hitung', $params);
    }
}

function randomRatarataNol($banyak){
    $average = 0;
    $solusi = [];
    $skala = 1;

    $ulang = true;

    while($ulang){
        for ($i = 0; $i < $banyak; $i++) {
            $random = ((float)rand()/(float)getrandmax() * ($skala * 2)) - $skala;
            $solusi[$i] = number_format($random, 2);
        }            
        $average = average($solusi);
        //var_dump($average);

        if($average < 0.5 && $average > -0.5){
//            echo $average;
            $ulang = false;
        }        
    }

    return $solusi;
    //var_dump($solusi);
    //var_dump($average);
}

function randomDistribusiNormal($banyak){
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

        $bilAcak[$i] = $b;
        //echo $i . " => " . $b . "<br>";
    }
    return $bilAcak;
}

function acakSolusi ($banyak){
    for ($i = 0; $i < $banyak; $i++) {
        $random = ((float)rand()/(float)getrandmax() * 20) - 10;
        $solusi[$i] = number_format($random, 2);
    }
//    $solusi = [0,0,0,0,0];
    return $solusi;
}

function hitungError($solusi, $dbd){
    $prediksi = 0;
    $total_kasus = 0;
    $error = 0;
    
    foreach ($dbd as $k => $v) {
        $total_kasus += $dbd[$k]->kasus;
        //$prediksi += ($solusi[0] * $dbd[$k]->ch) + ($solusi[1] * $dbd[$k]->hh) + ($solusi[2] * $dbd[$k]->abj) + ($solusi[3] * $dbd[$k]->hi) + ($solusi[4]);
        $prediksi += ($solusi[0] * $dbd[$k]->ch) + ($solusi[1] * $dbd[$k]->hh) + ($solusi[2] * $dbd[$k]->abj) + ($solusi[3] * $dbd[$k]->hi);
    }
    $error = sqrt((pow($total_kasus - $prediksi, 2))/sizeof($dbd));
    
    $hitungError['error'] = $error;
    $hitungError['prediksi'] = $prediksi;
    $hitungError['total_kasus'] = $total_kasus;

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