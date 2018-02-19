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
use backend\models\DbdFix;
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
       
    //hitung tiap kecamatan yg asli "JANGAN DIHAPUS !!!"
    public function actionHitungKecamatan3($metodebilacak, $metodeperhitungan) {
        $return['kecamatan'] = 'kecamatan';
        $return['metodebilacak'] = $metodebilacak;
        $return['metodeperhitungan'] = $metodeperhitungan;
        
        $kecamatan = Kecamatan::find()->asArray()->all();
        //$x=1;
        
        ini_set('max_execution_time', 30000);
        $time_start = microtime(true);

        for($iteration = 0; $iteration < sizeof($kecamatan); $iteration++){
            $dbd = DbdNormal::findBySql("
                SELECT *
                FROM dbd_normal
                WHERE tanggal NOT LIKE '%2012-%' AND id_kecamatan = ". $kecamatan[$iteration]['id'] ."
            ")->all();

            $dbd2 = DbdNormal::findBySql("
                SELECT *
                FROM dbd_normal
                WHERE tanggal LIKE '%2012-%' AND id_kecamatan = ". $kecamatan[$iteration]['id'] ."
            ")->all();

            //Acak Solusi
            if($metodeperhitungan == 'linear'){
                if($metodebilacak == 'biasa'){
                    $solusi = acakSolusi(5);
                } else if($metodebilacak == 'ratanol'){
                    $solusi = randomRatarataNol(5);
                } else {
                    $solusi = randomDistribusiNormal(5);
                }
            } else {
                if($metodebilacak == 'biasa'){
                    $solusi = acakSolusi(8);
                } else if($metodebilacak == 'ratanol'){
                    $solusi = randomRatarataNol(8);
                } else {
                    $solusi = randomDistribusiNormal(8);
                }
            }

            //perhitungan
            if($metodeperhitungan == 'linear'){
                //Hitung Error        
                $hitung = hitungLinear($solusi, $dbd);
                $error_tiap_data = $hitung['error_tiap_data'];
                $error = average($error_tiap_data);

                //Prediksi
                $hitung2 = hitungLinear($solusi, $dbd2);
                $error_tiap_data2 = $hitung2['error_tiap_data'];
                $error2 = average($error_tiap_data2);
            } else {
                //Hitung Error        
                $hitung = hitungKuadratik($solusi, $dbd);
                $error_tiap_data = $hitung['error_tiap_data'];
                $error = average($error_tiap_data);

                //Prediksi
                $hitung2 = hitungKuadratik($solusi, $dbd2);
                $error_tiap_data2 = $hitung2['error_tiap_data'];
                $error2 = average($error_tiap_data2);
            }

            //Penetapan Smin dan Emin
            $solusi_min = $solusi;
            $error_min = $error;

            //Prediksi
            $error_min2 = $error2;

            //Inisialisasi K dan T
            $K = $error_min * 10;
            //$T = 0.8;
            $T = 0.1; $T0 = 0.1; $Tn = 0.0001;

            //Prediksi
            $K2 = $error_min2 * 10;

            //prediksi min
            $prediksi_min = average($hitung['prediksi_tiap_data']);

            //Prediksi
            $prediksi_min2 = average($hitung2['prediksi_tiap_data']);

            $max_iterasi = 50;
            for ($iterasi = 0; $iterasi < $max_iterasi; $iterasi++){
                //Acak Solusi
                if($metodeperhitungan == 'linear'){
                    if($metodebilacak == 'biasa'){
                        $solusi = acakSolusi(5);
                    } else if($metodebilacak == 'ratanol'){
                        $solusi = randomRatarataNol(5);
                    } else {
                        $solusi = randomDistribusiNormal(5);
                    }
                } else {
                    if($metodebilacak == 'biasa'){
                        $solusi = acakSolusi(8);
                    } else if($metodebilacak == 'ratanol'){
                        $solusi = randomRatarataNol(8);
                    } else {
                        $solusi = randomDistribusiNormal(8);
                    }
                }

                if($metodeperhitungan == 'linear'){
                    //Hitung Error
                    $hitung = hitungLinear($solusi, $dbd);
                    $error = average($hitung['error_tiap_data']);
                    $prediksi = average($hitung['prediksi_tiap_data']);

                    //Prediksi
                    $hitung2 = hitungLinear($solusi, $dbd2);
                    $error2 = average($hitung2['error_tiap_data']);
                    $prediksi2 = average($hitung2['prediksi_tiap_data']);
                } else {
                    //Hitung Error
                    $hitung = hitungKuadratik($solusi, $dbd);
                    $error = average($hitung['error_tiap_data']);
                    $prediksi = average($hitung['prediksi_tiap_data']);

                    //Prediksi
                    $hitung2 = hitungKuadratik($solusi, $dbd2);
                    $error2 = average($hitung2['error_tiap_data']);
                    $prediksi2 = average($hitung2['prediksi_tiap_data']);
                }

                //Pengubahan Smin dan Emin
                $r = (float)rand()/(float)getrandmax();
                if($K == 0){
                    $K = 1;
                }
                $exp = exp( -($error - $error_min)/($K * $T));

                //Prediksi
                if($K2 == 0){
                    $K2 = 1;
                }
                $exp2 = exp( -($error2 - $error_min2)/($K2 * $T));

                if($r < $exp){ //Simulated Annealing
//                if($error_min > $error){ //Montecarlo
                    $solusi_min = $solusi;
                    $error_min = $error;
                    $prediksi_min = $prediksi;

                    $error_min2 = $error2;
                    $prediksi_min2 = $prediksi2;
                }

                //Pengubahan Temperature
                $coolingRate = 0.2;
                // $T = $T * (1 - $coolingRate);
                $T = $T0 * pow($Tn/$T0, $iterasi/$max_iterasi);

                if(isset($prediksi_min2)){
                    $return['prediksi_min'] = abs($prediksi_min2);
                } else{
                    $return['prediksi_min'] = abs($prediksi2);
                    var_dump($iterasi ." : ". $prediksi2); die();
                }
                $return['t_akhir'] = $T;
                if($iterasi%100 == 0){
                    //ntar dibuka ya
                    //$params['error_min'][] = abs(sum($hitung2['kasus']) - $params['prediksi_min']);
                }
            }
            
            $data = DbdFix::findBySql("
                SELECT df.kasus as 'kasus'
                FROM dbd_fix df
                WHERE df.tanggal LIKE '%2012-01%' AND df.id_kecamatan = ".$kecamatan[$iteration]['id']."
                GROUP BY id_kecamatan
            ")->asArray()->one();
            $hitung2 = hitungLinear($solusi_min, $dbd2);

            $prediksi2 = denormalisasi($hitung2['prediksi_tiap_data']);
//            $return[$iteration]['solusi'] = $solusi_min;
            $avgprediksi = round(average($prediksi2));
            $err = abs($data['kasus'] - $avgprediksi);
            $return[$iteration]['table']['kecamatan'] = $kecamatan[$iteration]['nama_kecamatan'];
            $return[$iteration]['table']['kasus'] = $data['kasus'];
            $return[$iteration]['table']['prediksi'] = $avgprediksi;
            $return[$iteration]['table']['error'] = $err;
            
            $kml[$iteration]['table']['kecamatan'] = $kecamatan[$iteration]['nama_kecamatan'];
            $kml[$iteration]['table']['kasus'] = $data['kasus'];
            $kml[$iteration]['table']['prediksi'] = $avgprediksi;
            $kml[$iteration]['table']['error'] = $err;
            
            $kml_asli[$iteration]['table']['kecamatan'] = $kecamatan[$iteration]['nama_kecamatan'];
            $kml_asli[$iteration]['table']['kasus'] = $data['kasus'];
        }
//        Helper::vdump($return);
//        die("a");
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        
        createKMLKecamatan2($kml_asli);
        //abc
        createKMLKecamatan($return);
        
        return $return;
    }
    
    //hitung tiap kecamatan pilih bulan
    public function actionHitungKecamatan2($metodebilacak, $metodeperhitungan, $bulan) {
        $return['kecamatan'] = 'kecamatan';
        $return['metodebilacak'] = $metodebilacak;
        $return['metodeperhitungan'] = $metodeperhitungan;
        $return['bulan'] = $bulan;
        
        $kecamatan = Kecamatan::find()->asArray()->all();
        
        if($bulan < 10){
            $sbulan = '0'.$bulan;
        } else {
            $sbulan = ''.$bulan;
        }
        //$x=1;
        
        ini_set('max_execution_time', 30000);
        $time_start = microtime(true);

        for($iteration = 0; $iteration < sizeof($kecamatan); $iteration++){
            $dbd = DbdNormal::findBySql("
                SELECT *
                FROM dbd_normal
                WHERE tanggal < '2012-".$sbulan."-01' AND id_kecamatan = ". $kecamatan[$iteration]['id'] ."
            ")->all();

            $dbd2 = DbdNormal::findBySql("
                SELECT *
                FROM dbd_normal
                WHERE tanggal LIKE '%2012-".$sbulan."%' AND id_kecamatan = ". $kecamatan[$iteration]['id'] ."
            ")->all();

            //Acak Solusi
            if($metodeperhitungan == 'linear'){
                if($metodebilacak == 'biasa'){
                    $solusi = acakSolusi(5);
                } else if($metodebilacak == 'ratanol'){
                    $solusi = randomRatarataNol(5);
                } else {
                    $solusi = randomDistribusiNormal(5);
                }
            } else {
                if($metodebilacak == 'biasa'){
                    $solusi = acakSolusi(8);
                } else if($metodebilacak == 'ratanol'){
                    $solusi = randomRatarataNol(8);
                } else {
                    $solusi = randomDistribusiNormal(8);
                }
            }

            //perhitungan
            if($metodeperhitungan == 'linear'){
                //Hitung Error        
                $hitung = hitungLinear($solusi, $dbd);
                $error_tiap_data = $hitung['error_tiap_data'];
                $error = average($error_tiap_data);

                //Prediksi
                $hitung2 = hitungLinear($solusi, $dbd2);
                $error_tiap_data2 = $hitung2['error_tiap_data'];
                $error2 = average($error_tiap_data2);
            } else {
                //Hitung Error        
                $hitung = hitungKuadratik($solusi, $dbd);
                $error_tiap_data = $hitung['error_tiap_data'];
                $error = average($error_tiap_data);

                //Prediksi
                $hitung2 = hitungKuadratik($solusi, $dbd2);
                $error_tiap_data2 = $hitung2['error_tiap_data'];
                $error2 = average($error_tiap_data2);
            }

            //Penetapan Smin dan Emin
            $solusi_min = $solusi;
            $error_min = $error;

            //Prediksi
            $error_min2 = $error2;

            //Inisialisasi K dan T
            $K = $error_min * 10;
            //$T = 0.8;
            $T = 0.1; $T0 = 0.1; $Tn = 0.0001;

            //Prediksi
            $K2 = $error_min2 * 10;

            //prediksi min
            $prediksi_min = average($hitung['prediksi_tiap_data']);

            //Prediksi
            $prediksi_min2 = average($hitung2['prediksi_tiap_data']);

            $max_iterasi = 1000;
            for ($iterasi = 0; $iterasi < $max_iterasi; $iterasi++){
                //Acak Solusi
                if($metodeperhitungan == 'linear'){
                    if($metodebilacak == 'biasa'){
                        $solusi = acakSolusi(5);
                    } else if($metodebilacak == 'ratanol'){
                        $solusi = randomRatarataNol(5);
                    } else {
                        $solusi = randomDistribusiNormal(5);
                    }
                } else {
                    if($metodebilacak == 'biasa'){
                        $solusi = acakSolusi(8);
                    } else if($metodebilacak == 'ratanol'){
                        $solusi = randomRatarataNol(8);
                    } else {
                        $solusi = randomDistribusiNormal(8);
                    }
                }

                if($metodeperhitungan == 'linear'){
                    //Hitung Error
                    $hitung = hitungLinear($solusi, $dbd);
                    $error = average($hitung['error_tiap_data']);
                    $prediksi = average($hitung['prediksi_tiap_data']);

                    //Prediksi
                    $hitung2 = hitungLinear($solusi, $dbd2);
                    $error2 = average($hitung2['error_tiap_data']);
                    $prediksi2 = average($hitung2['prediksi_tiap_data']);
                } else {
                    //Hitung Error
                    $hitung = hitungKuadratik($solusi, $dbd);
                    $error = average($hitung['error_tiap_data']);
                    $prediksi = average($hitung['prediksi_tiap_data']);

                    //Prediksi
                    $hitung2 = hitungKuadratik($solusi, $dbd2);
                    $error2 = average($hitung2['error_tiap_data']);
                    $prediksi2 = average($hitung2['prediksi_tiap_data']);
                }

                //Pengubahan Smin dan Emin
                $r = (float)rand()/(float)getrandmax();
                if($K == 0){
                    $K = 1;
                }
                $exp = exp( -($error - $error_min)/($K * $T));

                //Prediksi
                if($K2 == 0){
                    $K2 = 1;
                }
                $exp2 = exp( -($error2 - $error_min2)/($K2 * $T));

                if($r < $exp){ //Simulated Annealing
//                if($error_min > $error){ //Montecarlo
                    $solusi_min = $solusi;
                    $error_min = $error;
                    $prediksi_min = $prediksi;

                    $error_min2 = $error2;
                    $prediksi_min2 = $prediksi2;
                }

                //Pengubahan Temperature
                $coolingRate = 0.2;
                // $T = $T * (1 - $coolingRate);
                $T = $T0 * pow($Tn/$T0, $iterasi/$max_iterasi);

                if(isset($prediksi_min2)){
                    $return['prediksi_min'] = abs($prediksi_min2);
                } else{
                    $return['prediksi_min'] = abs($prediksi2);
                    var_dump($iterasi ." : ". $prediksi2); die();
                }
                $return['t_akhir'] = $T;
                if($iterasi%100 == 0){
                    //ntar dibuka ya
                    //$params['error_min'][] = abs(sum($hitung2['kasus']) - $params['prediksi_min']);
                }
            }
            
            $data = DbdFix::findBySql("
                SELECT df.kasus as 'kasus'
                FROM dbd_fix df
                WHERE df.tanggal LIKE '%2012-".$sbulan."%' AND df.id_kecamatan = ".$kecamatan[$iteration]['id']."
                GROUP BY id_kecamatan
            ")->asArray()->one();
            if($metodeperhitungan == 'linear'){
                $hitung2 = hitungLinear($solusi_min, $dbd2);
            } else{
                $hitung2 = hitungKuadratik($solusi_min, $dbd2);
            }
//            echo $kecamatan[$iteration]['id']."<br>";
//            Helper::vdump($solusi_min);
//            echo "========";

            $prediksi2 = denormalisasi($hitung2['prediksi_tiap_data']);
//            $return[$iteration]['solusi'] = $solusi_min;
            $avgprediksi = round(average($prediksi2));
            $err = abs($data['kasus'] - $avgprediksi);
            $return[$iteration]['table']['kecamatan'] = $kecamatan[$iteration]['nama_kecamatan'];
            $return[$iteration]['table']['kasus'] = $data['kasus'];
            $return[$iteration]['table']['prediksi'] = $avgprediksi;
            $return[$iteration]['table']['error'] = $err;
            
            $kml[$iteration]['table']['kecamatan'] = $kecamatan[$iteration]['nama_kecamatan'];
            $kml[$iteration]['table']['kasus'] = $data['kasus'];
            $kml[$iteration]['table']['prediksi'] = $avgprediksi;
            $kml[$iteration]['table']['error'] = $err;
            
            $kml_asli[$iteration]['table']['kecamatan'] = $kecamatan[$iteration]['nama_kecamatan'];
            $kml_asli[$iteration]['table']['kasus'] = $data['kasus'];
        }
//        die();
//        Helper::vdump($return);
//        die("a");
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        
        createKMLKecamatan2($return);
        createKMLKecamatan($return);
        
        return $return;
    }
    
    //hitung multi bulan kecamatan
    public function actionHitungKecamatan($metodebilacak, $metodeperhitungan, $bulan) {
        ini_set('max_execution_time', 300000000);
        $time_start = microtime(true);
        
        $return['kecamatan'] = 'kecamatan';
        $return['metodebilacak'] = $metodebilacak;
        $return['metodeperhitungan'] = $metodeperhitungan;
        $return['bulan'] = $bulan;
        
        $saveprediksi = [];
        $iprediksi = 0;
        $bulan = (int)$bulan;
        
        $kecamatan = Kecamatan::find()->asArray()->all();
        
        for($b = 1; $b <= $bulan; $b++){
            if($b < 10){
                $sbulan = '0'.$b;
            } else {
                $sbulan = ''.$b;
            }
            
            for($iteration = 0; $iteration < sizeof($kecamatan); $iteration++){
                $dbd = DbdNormal::findBySql("
                    SELECT *
                    FROM dbd_normal
                    WHERE tanggal < '2012-".$sbulan."-01' AND id_kecamatan = ". $kecamatan[$iteration]['id'] ."
                ")->asArray()->all();
                if($b > 1){
                    for($i=1; $i<$b; $i++){
                        $dbd[35+$i]['kasus'] = $saveprediksi[$b-1][$kecamatan[$iteration]['id']];
                        //qwerty
                    }
                }

                $dbd2 = DbdNormal::findBySql("
                    SELECT *
                    FROM dbd_normal
                    WHERE tanggal LIKE '%2012-".$sbulan."-01%' AND id_kecamatan = ". $kecamatan[$iteration]['id'] ."
                ")->asArray()->all();

                //Acak Solusi
                if($metodeperhitungan == 'linear'){
                    if($metodebilacak == 'biasa'){
                        $solusi = acakSolusi(5);
                    } else {
                        $solusi = randomRatarataNol(5);
                    }
                } else {
                    if($metodebilacak == 'biasa'){
                        $solusi = acakSolusi(8);
                    } else{
                        $solusi = randomRatarataNol(8);
                    }
                }

                //perhitungan
                if($metodeperhitungan == 'linear'){
                    //Hitung Error        
                    $hitung = hitungLinear2($solusi, $dbd);
                    $error_tiap_data = $hitung['error_tiap_data'];
                    $error = average($error_tiap_data);

                    //Prediksi
                    $hitung2 = hitungLinear2($solusi, $dbd2);
                    $error_tiap_data2 = $hitung2['error_tiap_data'];
                    $error2 = average($error_tiap_data2);
                } else {
                    //Hitung Error        
                    $hitung = hitungKuadratik2($solusi, $dbd);
                    $error_tiap_data = $hitung['error_tiap_data'];
                    $error = average($error_tiap_data);

                    //Prediksi
                    $hitung2 = hitungKuadratik2($solusi, $dbd2);
                    $error_tiap_data2 = $hitung2['error_tiap_data'];
                    $error2 = average($error_tiap_data2);
                }

                //Penetapan Smin dan Emin
                $solusi_min = $solusi;
                $error_min = $error;

                //Prediksi
                $error_min2 = $error2;

                //Inisialisasi K dan T
                $K = $error_min * 10;
                //$T = 0.8;
                $T = 0.1; $T0 = 0.1; $Tn = 0.0001;

                //Prediksi
                $K2 = $error_min2 * 10;

                //prediksi min
                $prediksi_min = average($hitung['prediksi_tiap_data']);

                //Prediksi
                $prediksi_min2 = average($hitung2['prediksi_tiap_data']);

                $max_iterasi = 500;
                for ($iterasi = 0; $iterasi < $max_iterasi; $iterasi++){
                    //Acak Solusi
                    if($metodeperhitungan == 'linear'){
                        if($metodebilacak == 'biasa'){
                            $solusi = acakSolusi(5);
                        } else{
                            $solusi = randomRatarataNol(5);
                        }
                    } else {
                        if($metodebilacak == 'biasa'){
                            $solusi = acakSolusi(8);
                        } else{
                            $solusi = randomRatarataNol(8);
                        }
                    }

                    if($metodeperhitungan == 'linear'){
                        //Hitung Error
                        $hitung = hitungLinear2($solusi, $dbd);
                        $error = average($hitung['error_tiap_data']);
                        $prediksi = average($hitung['prediksi_tiap_data']);

                        //Prediksi
                        $hitung2 = hitungLinear2($solusi, $dbd2);
                        $error2 = average($hitung2['error_tiap_data']);
                        $prediksi2 = average($hitung2['prediksi_tiap_data']);
                    } else {
                        //Hitung Error
                        $hitung = hitungKuadratik2($solusi, $dbd);
                        $error = average($hitung['error_tiap_data']);
                        $prediksi = average($hitung['prediksi_tiap_data']);

                        //Prediksi
                        $hitung2 = hitungKuadratik2($solusi, $dbd2);
                        $error2 = average($hitung2['error_tiap_data']);
                        $prediksi2 = average($hitung2['prediksi_tiap_data']);
                    }

                    //Pengubahan Smin dan Emin
                    $r = (float)rand()/(float)getrandmax();
                    if($K == 0){
                        $K = 1;
                    }
                    $exp = exp( -($error - $error_min)/($K * $T));

                    //Prediksi
                    if($K2 == 0){
                        $K2 = 1;
                    }
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
                    // $T = $T * (1 - $coolingRate);
                    $T = $T0 * pow($Tn/$T0, $iterasi/$max_iterasi);

                    if(isset($prediksi_min2)){
                        $return['prediksi_min'] = abs($prediksi_min2);
                    } else{
                        $return['prediksi_min'] = abs($prediksi2);
                        var_dump($iterasi ." : ". $prediksi2); die();
                    }
                    $return['t_akhir'] = $T;
                    if($iterasi%100 == 0){
                        //ntar dibuka ya
                        //$params['error_min'][] = abs(sum($hitung2['kasus']) - $params['prediksi_min']);
                    }
                }

                $data = DbdFix::findBySql("
                    SELECT df.kasus as 'kasus', df.id_kecamatan as 'id_kecamatan'
                    FROM dbd_fix df
                    WHERE df.tanggal LIKE '%2012-".$sbulan."%' AND df.id_kecamatan = ".$kecamatan[$iteration]['id']."
                    GROUP BY id_kecamatan
                ")->asArray()->one();
                
                if($metodeperhitungan == 'linear'){
                    $hitung2 = hitungLinear2($solusi_min, $dbd2);
                } else {
                    $hitung2 = hitungKuadratik2($solusi_min, $dbd2);
                }
                $avgsaveprediksi = number_format(average($hitung2['prediksi_tiap_data']), 2);
                if($avgsaveprediksi < 0){
                    $avgsaveprediksi = 0;
                }
                
                
                $prediksi2 = denormalisasi($hitung2['prediksi_tiap_data']);
    //            $return[$iteration]['solusi'] = $solusi_min;
                $avgprediksi = round(average($prediksi2));
                $err = abs($data['kasus'] - $avgprediksi);
                $return[$iteration]['table']['kecamatan'] = $kecamatan[$iteration]['nama_kecamatan'];
                $return[$iteration]['table']['kasus'] = $data['kasus'];
                $return[$iteration]['table']['prediksi'] = $avgprediksi;
                $return[$iteration]['table']['error'] = $err;
                
                //$saveprediksi[$b][$data['id_kecamatan']]['id_kecamatan'] = $data['id_kecamatan'];
                $saveprediksi[$b][$data['id_kecamatan']] = $avgsaveprediksi;

                $kml[$iteration]['table']['kecamatan'] = $kecamatan[$iteration]['nama_kecamatan'];
                $kml[$iteration]['table']['kasus'] = $data['kasus'];
                $kml[$iteration]['table']['prediksi'] = $avgprediksi;
                $kml[$iteration]['table']['error'] = $err;

                $kml_asli[$iteration]['table']['kecamatan'] = $kecamatan[$iteration]['nama_kecamatan'];
                $kml_asli[$iteration]['table']['kasus'] = $data['kasus'];
                
                $iprediksi++;
                
//                Helper::vdump($dbd);
//                echo "=============";
//                if($b == 4){
//                    die();
//                }
            }
        }
        
//        Helper::vdump($return);
//        die("a");
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        
        createKMLKecamatan2($return);
        createKMLKecamatan($return);
        
        return $return;
    }
    
    //hitung seluruh jember yg asli "JANGAN DIHAPUS !!!"
    public function actionHitungJember2($metodeperhitungan, $metodebilacak) {
        $return['kecamatan'] = 'jember';
        $return['metodebilacak'] = $metodebilacak;
        $return['metodeperhitungan'] = $metodeperhitungan;
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

        $max_iterasi = 10;
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
        
        $return['solusi'] = $solusi_min;
        
        //denormalisasi hasil (pengembalian ke nilai fix nya)
        //Helper::vdump($arr_prediksi_tiap_kecamatan);
        $prediksi_tiap_kecamatan = denormalisasi($arr_prediksi_tiap_kecamatan);
        //Helper::vdump($prediksi_tiap_kecamatan);
        
        //Pengisian parameter di VIEW nya
        for($id_kecamatan = 1; $id_kecamatan <= sizeof($kecamatan); $id_kecamatan++){
            $data = DbdFix::findBySql("
                SELECT kec.nama_kecamatan as 'kecamatan',
                    df.kasus as 'kasus'
                FROM dbd_fix df
                JOIN kecamatan kec ON df.id_kecamatan = kec.id
                WHERE df.tanggal LIKE '%2012-01%' AND df.id_kecamatan = ".$id_kecamatan."
                GROUP BY id_kecamatan
            ")->asArray()->one();
            $return['table'][$id_kecamatan - 1]['kecamatan'] = $data['kecamatan'];
            $return['table'][$id_kecamatan - 1]['kasus'] = $data['kasus'];
            $return['table'][$id_kecamatan - 1]['prediksi'] = $prediksi_tiap_kecamatan[$id_kecamatan - 1];
            $return['table'][$id_kecamatan - 1]['error'] = abs($data['kasus'] - $prediksi_tiap_kecamatan[$id_kecamatan - 1]);
        }
        
        createKMLJember($return['table']);
        
//        echo "Solusi :";
//        Helper::vdump($solusi_min);
//        
//        echo "Kasus tiap kecamatan :";
//        Helper::vdump($arr_kasus_tiap_kecamatan);
//        
//        echo "Prediksi tiap kecamatan :";
//        Helper::vdump($arr_prediksi_tiap_kecamatan);
//        
//        echo "Error tiap kecamatan :";
//        Helper::vdump($arr_error_tiap_kecamatan);
//        
//        echo "Params :";
//        Helper::vdump($return);
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        //echo $time . '<br>';
        
        //return $this->render('hitung', ['params' => $params]);
        return $return;
    }
    
    //hitung muti bulan jember
    public function actionHitungJember($metodeperhitungan, $metodebilacak, $bulan) {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 30000);
        $time_start = microtime(true);
        
        $return['kecamatan'] = 'jember';
        $return['metodebilacak'] = $metodebilacak;
        $return['metodeperhitungan'] = $metodeperhitungan;
        $return['bulan'] = $bulan;
        
        $saveprediksi = [];
        $iprediksi = 0;
        $bulan = (int)$bulan;
//        $tes = createKML();
        
        $kecamatan = Kecamatan::find()->all();
        
        for($b = 1; $b <= $bulan; $b++){
            if($b < 10){
                $sbulan = '0'.$b;
            } else {
                $sbulan = ''.$b;
            }

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
                        WHERE tanggal < '2012-".$sbulan."-01' AND id_kecamatan = ". $id_kecamatan ."
                    ")->asArray()->all();
                    if($b > 1){
	                for($i=1; $i<$b; $i++){
	                    $dbd[35+$i]['kasus'] = $saveprediksi[$b-1][$id_kecamatan];
	                    //qwerty
	                }
	            }

                    $dbd2 = DbdNormal::findBySql("
                        SELECT *
                        FROM dbd_normal
                        WHERE tanggal LIKE '%2012-".$sbulan."%' AND id_kecamatan = ". $id_kecamatan ."
                    ")->asArray()->all();

                    //Hitung Error
                    $hitung = hitungLinear2($solusi, $dbd);
                    $error_tiap_data = $hitung['error_tiap_data'];
                    $arr_error[] = sum($error_tiap_data) / sizeof($dbd);

                    //Prediksi
                    $hitung2 = hitungLinear2($solusi, $dbd2);
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
                        WHERE tanggal < '2012-".$sbulan."-01' AND id_kecamatan = ". $id_kecamatan ."
                    ")->asArray()->all();
                    if($b > 1){
	                for($i=1; $i<$b; $i++){
	                    $dbd[35+$i]['kasus'] = $saveprediksi[$b-1][$id_kecamatan];
	                    //qwerty
	                }
	            }

                    $dbd2 = DbdNormal::findBySql("
                        SELECT *
                        FROM dbd_normal
                        WHERE tanggal LIKE '%2012-".$sbulan."%' AND id_kecamatan = ". $id_kecamatan ."
                    ")->asArray()->all();

                    //Hitung Error
                    $hitung = hitungKuadratik2($solusi, $dbd);
                    $error_tiap_data = $hitung['error_tiap_data'];
                    $arr_error[] = sum($error_tiap_data) / sizeof($dbd);

                    //Prediksi
                    $hitung2 = hitungKuadratik2($solusi, $dbd2);
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
            $T = 0.1; $T0 = 0.1; $Tn = 0.0000001;

            //Prediksi
            $K2 = $error_min2 * 10;

            //menghitung rata2 prediksi min
            $prediksi_min = sum($hitung['prediksi_tiap_data']) / sizeof($dbd);

            //menghitung rata2 Prediksi
            $prediksi_min2 = sum($hitung2['prediksi_tiap_data']) / sizeof($dbd2);

            $max_iterasi = 1000;
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
                            WHERE tanggal < '2012-".$sbulan."-01' AND id_kecamatan = ". $id_kecamatan ."
                        ")->asArray()->all();
                        if($b > 1){
                            for($i=1; $i<$b; $i++){
                                $dbd[35+$i]['kasus'] = $saveprediksi[$b-1][$id_kecamatan];
                                //qwerty
                            }
                        }

                        $dbd2 = DbdNormal::findBySql("
                            SELECT *
                            FROM dbd_normal
                            WHERE tanggal LIKE '%2012-".$sbulan."%' AND id_kecamatan = ". $id_kecamatan ."
                        ")->asArray()->all();

                        //Hitung Error
                        $hitung = hitungLinear2($solusi, $dbd);
                        $error_tiap_data = $hitung['error_tiap_data'];
                        $arr_error[] = sum($error_tiap_data) / sizeof($dbd);
                        $prediksi_tiap_data = $hitung['prediksi_tiap_data'];
                        $arr_prediksi[] = sum($prediksi_tiap_data) / sizeof($dbd);
                        $arr_kasus[] = sum($hitung['kasus']);

                        //Prediksi
                        $hitung2 = hitungLinear2($solusi, $dbd2);
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
                            WHERE tanggal < '2012-".$sbulan."-01' AND id_kecamatan = ". $id_kecamatan ."
                        ")->asArray()->all();
                        if($b > 1){
                            for($i=1; $i<$b; $i++){
                                $dbd[35+$i]['kasus'] = $saveprediksi[$b-1][$id_kecamatan];
                                //qwerty
                            }
                        }

                        $dbd2 = DbdNormal::findBySql("
                            SELECT *
                            FROM dbd_normal
                            WHERE tanggal LIKE '%2012-".$sbulan."%' AND id_kecamatan = ". $id_kecamatan ."
                        ")->asArray()->all();

                        //Hitung Error
                        $hitung = hitungKuadratik2($solusi, $dbd);
                        $error_tiap_data = $hitung['error_tiap_data'];
                        $arr_error[] = sum($error_tiap_data) / sizeof($dbd);
                        $prediksi_tiap_data = $hitung['prediksi_tiap_data'];
                        $arr_prediksi[] = sum($prediksi_tiap_data) / sizeof($dbd);
                        $arr_kasus[] = sum($hitung['kasus']);

                        //Prediksi
                        $hitung2 = hitungKuadratik2($solusi, $dbd2);
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
//                Helper::vdump($T);

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
                    WHERE tanggal LIKE '%2012-".$sbulan."%' AND id_kecamatan = ". $id_kecamatan ."
                ")->asArray()->all();

                if($metodeperhitungan == 'linear'){
                    $hitung2 = hitungLinear2($solusi_min, $dbd2);
                } else{
                    $hitung2 = hitungKuadratik2($solusi_min, $dbd2);
                }
                $arr_kasus_tiap_kecamatan[] = sum($hitung2['kasus']);
                $arr_prediksi_tiap_kecamatan[] = average($hitung2['prediksi_tiap_data']);
                $arr_error_tiap_kecamatan[] = abs(sum($hitung2['kasus']) - average($hitung2['prediksi_tiap_data']));
                
                $avgsaveprediksi = number_format(average($hitung2['prediksi_tiap_data']), 2);
                if($avgsaveprediksi < 0){
                    $avgsaveprediksi = 0;
                }
                $saveprediksi[$b][$id_kecamatan] = $avgsaveprediksi;
                
                $iprediksi++;
            }
//            Helper::vdump($dbd);
//            echo "a===================a";
//            if($b == 4){
//                die();
//            }

            $return['solusi'] = $solusi_min;

            //denormalisasi hasil (pengembalian ke nilai fix nya)
            $prediksi_tiap_kecamatan = denormalisasi($arr_prediksi_tiap_kecamatan);

            //Pengisian parameter di VIEW nya
            for($id_kecamatan = 1; $id_kecamatan <= sizeof($kecamatan); $id_kecamatan++){
                $data = DbdFix::findBySql("
                    SELECT kec.nama_kecamatan as 'kecamatan',
                        df.kasus as 'kasus'
                    FROM dbd_fix df
                    JOIN kecamatan kec ON df.id_kecamatan = kec.id
                    WHERE df.tanggal LIKE '%2012-".$sbulan."%' AND df.id_kecamatan = ".$id_kecamatan."
                    GROUP BY id_kecamatan
                ")->asArray()->one();
                $return['table'][$id_kecamatan - 1]['kecamatan'] = $data['kecamatan'];
                $return['table'][$id_kecamatan - 1]['kasus'] = $data['kasus'];
                $return['table'][$id_kecamatan - 1]['prediksi'] = $prediksi_tiap_kecamatan[$id_kecamatan - 1];
                $return['table'][$id_kecamatan - 1]['error'] = abs($data['kasus'] - $prediksi_tiap_kecamatan[$id_kecamatan - 1]);
            }
        }
        
//        var_dump($T);
//        die("aaaa");
        
        createKMLJember($return['table'], 'prediksi');
        createKMLJember($return['table'], 'kasus');
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        //echo $time . '<br>';
        
        //return $this->render('hitung', ['params' => $params]);
        return $return;
    }
    
    public function actionGetPrediksi() {
        $kecamatan = $_GET['kecamatan'];
        $metodeperhitungan = $_GET['metodeperhitungan'];
        $metodebilacak = $_GET['metodebilacak'];
        $bulan = $_GET['bulan'];
        
        if($kecamatan == 'jember'){
            $hitung = $this->actionHitungJember($metodebilacak, $metodeperhitungan, $bulan);
            return $this->render('hitung', ['params' => $hitung]);
        } else {
            $hitung = $this->actionHitungKecamatan($metodebilacak, $metodeperhitungan, $bulan);
            return $this->render('hitung', ['params' => $hitung]);
        }
    }
    
    public function actionDashboard(){
        $kecamatan = Kecamatan::find()->asArray()->all();
        
        for($i=0; $i<sizeof($kecamatan); $i++){
            $data = DbdFix::findBySql("
                SELECT sum(df.kasus) as 'kasus'
                FROM dbd_fix df
                WHERE df.id_kecamatan = ".$kecamatan[$i]['id']."
                GROUP BY id_kecamatan
            ")->asArray()->one();
            $hasil[]['kasus'] = $data['kasus'];
        }
        
        createKMLKecamatan3($hasil);
//        Helper::vdump($hasil);
//        die("bnm,");
    }
}

function acakSolusi ($banyak){
//    srand(mktime());
    for ($i = 0; $i < $banyak; $i++) {
        $random = ((float)rand()/(float)getrandmax() * 2) - 1;
        $solusi[$i] = number_format($random, 3);
    }
    return $solusi;
}

function randomRatarataNol($banyak){
    srand(mktime());
    $average = 0;
    $solusi = [];
    $skala = 0.5;

    $ulang = true;

    while($ulang){
        for ($i = 0; $i < $banyak; $i++) {
            $random = ((float)rand()/(float)getrandmax() * ($skala * 3)) - $skala;
            $solusi[$i] = number_format($random, 3);
        }
        $average = average($solusi);

        if($average < 0.02 && $average > -0.02){
            $ulang = false;
        }
    }
    //$solusi = [0,0,0,0,0,0,0];

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
            $b = (20 * $a / 100) - 10;
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

//jangan dihapus
function hitungLinear($solusi, $dbd){
    $total_kasus = 0;
    $error = 0;
    $hitungError['grandtotal_prediksi'] = 0;
    $hitungError['grandtotal_error'] = 0;
    
    foreach ($dbd as $k => $v) {
        $total_kasus += $dbd[$k]->kasus;
        $prediksi = ($solusi[0] * $dbd[$k]->ch) + ($solusi[1] * $dbd[$k]->hh) + ($solusi[2] * $dbd[$k]->abj) + ($solusi[3] * $dbd[$k]->hi);
//        echo $prediksi;
//        die("aaa");
        
        $pre[] = $prediksi;
        //$error = abs($dbd[$k]->kasus - $prediksi);
        $error = pow($prediksi - $dbd[$k]->kasus, 2);
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

//buat multi bulan
function hitungLinear2($solusi, $dbd){
    $total_kasus = 0;
    $error = 0;
    $hitungError['grandtotal_prediksi'] = 0;
    $hitungError['grandtotal_error'] = 0;
    
    foreach ($dbd as $k => $v) {
        $total_kasus += $dbd[$k]['kasus'];
        $prediksi = ($solusi[0] * $dbd[$k]['ch']) + ($solusi[1] * $dbd[$k]['hh']) + ($solusi[2] * $dbd[$k]['abj']) + ($solusi[3] * $dbd[$k]['hi']);
        //abc
        
        
        $pre[] = $prediksi;
        //$error = abs($dbd[$k]->kasus - $prediksi);
        $error = pow($prediksi - $dbd[$k]['kasus'], 2);
        $error_prediksi[] = $error;
        
        $hitungError['ch'][] = $dbd[$k]['ch'];
        $hitungError['hh'][] = $dbd[$k]['hh'];
        $hitungError['abj'][] = $dbd[$k]['abj'];
        $hitungError['hi'][] = $dbd[$k]['hi'];
        $hitungError['kasus'][] = $dbd[$k]['kasus'];
        $hitungError['prediksi_tiap_data'][] = number_format($prediksi, 2);
        $hitungError['error_tiap_data'][] = number_format($error, 2);
    }
    return $hitungError;
}

//jangan dihapus
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

//buat multi bulan
function hitungKuadratik2($solusi, $dbd){    
    $total_kasus = 0;
    $error = 0;
    $hitungError['grandtotal_prediksi'] = 0;
    $hitungError['grandtotal_error'] = 0;
    
    foreach ($dbd as $k => $v) {
        $total_kasus += $dbd[$k]['kasus'];
        $prediksi = ($solusi[0] * pow($dbd[$k]['ch'], 2)) + ($solusi[1] * $dbd[$k]['ch']) + 
                ($solusi[2] * pow($dbd[$k]['hh'], 2)) + ($solusi[3] * $dbd[$k]['hh']) + 
                ($solusi[4] * pow($dbd[$k]['abj'], 2)) + ($solusi[5] * $dbd[$k]['abj']) +
                ($solusi[6] * pow($dbd[$k]['hi'], 2)) + ($solusi[7] * $dbd[$k]['hi']);
        
        $pre[] = $prediksi;
        $error = abs($dbd[$k]['kasus'] - $prediksi);
        $error_prediksi[] = $error;
        
        $hitungError['ch'][] = $dbd[$k]['ch'];
        $hitungError['hh'][] = $dbd[$k]['hh'];
        $hitungError['abj'][] = $dbd[$k]['abj'];
        $hitungError['hi'][] = $dbd[$k]['hi'];
        $hitungError['kasus'][] = $dbd[$k]['kasus'];
        $hitungError['prediksi_tiap_data'][] = number_format($prediksi, 2);
        $hitungError['error_tiap_data'][] = number_format($error, 2);
    }
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

function denormalisasi($data){
    $dbdfix = DbdFix::findBySql("
        SELECT max(kasus) as max, min(kasus) as min
        FROM dbd_fix
    ")->asArray()->one();
    
    //data = newdata / ((newmax-newmin)/(max-min)+newmin) - min
    $newmax = 1;
    $newmin = 0;
    
    $max = $dbdfix['max'];
    $min = $dbdfix['min'];
    
    foreach ($data as $key => $value) {
        $hasil = round(number_format(($value / (($newmax - $newmin) / ($max - $min) + $newmin) - $min), 2));
        if($hasil <= 0){
            $return[] = 0;
        } else {
            $return[] = $hasil;
        }
    }
    return $return;
}

function createKMLKecamatan($data){
//    Helper::vdump($data);
//    die("--");
    for($i=0; $i < sizeof($data)-6; $i++){
        if($data[$i]['table']['prediksi'] == 0){
            $style[] = 'nol';
        } else if($data[$i]['table']['prediksi'] > 0 && $data[$i]['table']['prediksi'] <= 3){
            $style[] = 'rendah';
        } else if($data[$i]['table']['prediksi'] > 3 && $data[$i]['table']['prediksi'] <= 8){
            $style[] = 'sedang';
        } else{
            $style[] = 'tinggi';
        }
    }
    
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
<description>Prediksi '.$data[$k]['table']['prediksi'].'</description>
<LookAt>
<longitude>113.62262706</longitude>
<latitude>-8.07996199</latitude>
<range>27845</range>
</LookAt>
<styleUrl>#PolyStyle'.$style[$k].'</styleUrl>
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

<Style id="PolyStylenol">
<LabelStyle>
<color>00000000</color>
<scale>0.000000</scale>
</LabelStyle>
<LineStyle>
<color>ff666666</color>
<width>0.750000</width>
</LineStyle>
<PolyStyle>
<color>78ffffff</color>
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
<color>7800ff00</color>
<outline>1</outline>
</PolyStyle>
</Style>

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
<color>7800ffff</color>
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
<color>780000ff</color>
<outline>1</outline>
</PolyStyle>
</Style>

</Document>
</kml>';
    
    file_put_contents("../kml/test.kml",$kml);
}

//create kml berdasarkan data asli
function createKMLKecamatan2($data){
//    Helper::vdump($data);
//    die("aaa");
    for($i=0; $i < sizeof($data)-6; $i++){
        if($data[$i]['table']['kasus'] == 0){
            $style[] = 'nol';
        } else if($data[$i]['table']['kasus'] > 0 && $data[$i]['table']['kasus'] <= 3){
            $style[] = 'rendah';
        } else if($data[$i]['table']['kasus'] > 3 && $data[$i]['table']['kasus'] <= 8){
            $style[] = 'sedang';
        } else{
            $style[] = 'tinggi';
        }
    }
    
//    Helper::vdump($style);
//    die("aaa");
    
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
<description>Kasus '.$data[$k]['table']['kasus'].'</description>
<LookAt>
<longitude>113.62262706</longitude>
<latitude>-8.07996199</latitude>
<range>27845</range>
</LookAt>
<styleUrl>#PolyStyle'.$style[$k].'</styleUrl>
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

<Style id="PolyStylenol">
<LabelStyle>
<color>00000000</color>
<scale>0.000000</scale>
</LabelStyle>
<LineStyle>
<color>ff666666</color>
<width>0.750000</width>
</LineStyle>
<PolyStyle>
<color>78ffffff</color>
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
<color>7800ff00</color>
<outline>1</outline>
</PolyStyle>
</Style>

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
<color>7800ffff</color>
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
<color>780000ff</color>
<outline>1</outline>
</PolyStyle>
</Style>

</Document>
</kml>';
    
    file_put_contents("../kml/kecamatan.kml",$kml);
}

//create kml cuma buat dashboard
function createKMLKecamatan3($data){
    
    for($i=0; $i < sizeof($data); $i++){
        if($data[$i]['kasus'] <= 50){
            $style[] = 'rendah';
        } else if($data[$i]['kasus'] > 50 && $data[$i]['kasus'] <= 100){
            $style[] = 'sedang';
        } else{
            $style[] = 'tinggi';
        }
    }
    
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
<description>Total kasus '.$data[$k]['kasus'].'</description>
<LookAt>
<longitude>113.62262706</longitude>
<latitude>-8.07996199</latitude>
<range>27845</range>
</LookAt>
<styleUrl>#PolyStyle'.$style[$k].'</styleUrl>
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
<color>7800ff00</color>
<outline>1</outline>
</PolyStyle>
</Style>

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
<color>7800ffff</color>
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
<color>780000ff</color>
<outline>1</outline>
</PolyStyle>
</Style>

</Document>
</kml>';
    
    file_put_contents("../kml/dashboard.kml",$kml);
}

function createKMLJember($data, $buat){
//    Helper::vdump($data);
//    die("bhjnkm;");
    if($buat == 'prediksi'){
        for($i=0; $i < sizeof($data); $i++){
            if($data[$i]['prediksi'] <= 3){
                $style[] = 'rendah';
            } else if($data[$i]['prediksi'] > 3 && $data[$i]['prediksi'] <= 8){
                $style[] = 'sedang';
            } else{
                $style[] = 'tinggi';
            }
        }
    } else{
        for($i=0; $i < sizeof($data); $i++){
            if($data[$i]['kasus'] <= 3){
                $style[] = 'rendah';
            } else if($data[$i]['kasus'] > 3 && $data[$i]['kasus'] <= 8){
                $style[] = 'sedang';
            } else{
                $style[] = 'tinggi';
            }
        }
    }
    
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
<styleUrl>#PolyStyle'.$style[$k].'</styleUrl>
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
<color>7800ff00</color>
<outline>1</outline>
</PolyStyle>
</Style>

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
<color>7800ffff</color>
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
<color>780000ff</color>
<outline>1</outline>
</PolyStyle>
</Style>

</Document>
</kml>';
    
    if($buat == 'prediksi'){
        file_put_contents("../kml/test.kml",$kml);
    } else{
        file_put_contents("../kml/kecamatan.kml",$kml);
    }
}

function displayTabel($data){
    Helper::vdump($data);
    
    $i=1;
    echo "<table border=1>";
        echo "<tr>";
            echo "<td>Kecamatan</td>";
            echo "<td>Kasus</td>";
            echo "<td>1</td>";
            echo "<td>2</td>";
            echo "<td>3</td>";
            echo "<td>4</td>";
            echo "<td>5</td>";
            echo "<td>6</td>";
            echo "<td>7</td>";
            echo "<td>8</td>";
            echo "<td>9</td>";
            echo "<td>10</td>";
        echo "</tr>";
        
        while($i<=310){
        echo "<tr>";
            echo "<td>".$data[$i]['table']['kecamatan']."</td>";
            echo "<td>".$data[$i]['table']['kasus']."</td>";
            echo "<td>".$data[$i]['table']['prediksi']."</td>";
            echo "<td>".$data[$i+1]['table']['prediksi']."</td>";
            echo "<td>".$data[$i+2]['table']['prediksi']."</td>";
            echo "<td>".$data[$i+3]['table']['prediksi']."</td>";
            echo "<td>".$data[$i+4]['table']['prediksi']."</td>";
            echo "<td>".$data[$i+5]['table']['prediksi']."</td>";
            echo "<td>".$data[$i+6]['table']['prediksi']."</td>";
            echo "<td>".$data[$i+7]['table']['prediksi']."</td>";
            echo "<td>".$data[$i+8]['table']['prediksi']."</td>";
            echo "<td>".$data[$i+9]['table']['prediksi']."</td>";
        echo "</tr>";
        
        $i = $i+10;;
        }
    echo "</table>";
    
    die("display");
}