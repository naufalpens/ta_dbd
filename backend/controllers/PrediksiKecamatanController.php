<?php

namespace backend\controllers;

use Yii;
use backend\models\PrediksiKecamatan;
use backend\models\PrediksiKecamatanSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\DbdNormal;

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
        ini_set('max_execution_time', 300);
        
        $id_kecamatan = 2;        
//        $dbd = DbdNormal::find()->all();
                
        $dbd = DbdNormal::findBySql("
            SELECT *
            FROM dbd_normal
            WHERE tanggal NOT LIKE '%2012-%' 
        ")->all();
        
//        $dbd_prediksi = DbdNormal::findBySql("
//            SELECT *
//            FROM dbd_normal
//            WHERE tanggal LIKE '%2012-%'
//        ")->all();
//        
//        $total_dbd_prediksi = DbdNormal::findBySql("
//            SELECT SUM(kasus)
//            FROM dbd_normal
//            WHERE tanggal LIKE '%2012-%'
//        ")->all();
        
//        var_dump($total_dbd_prediksi); die();
//        var_dump(sizeof($dbd), sizeof($dbd_prediksi)); die();
         
        //Acak Solusi
        $solusi = acakSolusi(4);
        
        //Hitung Error
        $hitung = hitungError($solusi, $dbd);
        $error = $hitung['error'];
        
        //Penetapan Smin dan Emin
        $solusi_min = $solusi;
        $error_min = $error;
        
        //Inisialisasi K dan T
        $K = $error_min * 10;
        $T = 0.8;
        
        //prediksi min
        $prediksi_min = $hitung['prediksi'];
        
        for ($iterasi = 0; $iterasi < 50; $iterasi++){
            //Acak Solusi
            $solusi = acakSolusi(4);

            //Hitung Error
            $hitung = hitungError($solusi, $dbd); 
            $error = $hitung['error'];
            $prediksi = $hitung['prediksi'];

            //Pengubahan Smin dan Emin
            $r = (float)rand()/(float)getrandmax();
            $exp = exp( -($error - $error_min)/($K * $T));
                        
            
            if($r < $exp){ //Simulated Annealing
//            if($error_min > $error){ //Montecarlo
                $solusi_min = $solusi;
                $error_min = $error;
                $prediksi_min = $prediksi;
            }
            
            //Pengubahan Temperature
            $coolingRate = 0.12;
            $T = $T * (1 - $coolingRate);
        
            //Pengisian Prams untuk ditampilkan ke View
            $params['kasus'] = $hitung['total_kasus'];
            $params['solusi_min'] = $solusi_min;
            if(isset($prediksi_min)){
                $params['prediksi_min'] = $prediksi_min;                
            } else{
                $params['prediksi_min'] = $prediksi;
                var_dump($iterasi ." : ". $prediksi); die();
            }
            $params['t_akhir'] = $T;
            $params['error_min'] = $error_min;
            
            echo $iterasi+1 . ". Prediksi : " . $params['prediksi_min'] . " - - - ";
            echo "Kasus : " . $params['kasus'] . " - - - ";
            echo "Error : " . $params['error_min'] . "<br>";
        }
//        echo $params['prediksi_min']."<br>";
        die();

//        $params['kasus'] = $total_kasus;
//        $params['error_min'] = $error_min;
//        $params['solusi_min'] = $solusi_min;
//        $params['prediksi_min'] = $prediksi_min;
//        $params['t_akhir'] = $T;
        
//        return $this->render('hitung', ['error_min' => $error_min, 'solusi_min' => $solusi_min]);
        return $this->render('hitung', $params);
    }    
    
    public function actionHitungKuadratik() {
        $dbd = DbdNormal::find()->all();

        //Acak Solusi
        for ($i = 0; $i < 8; $i++) {
            $random = (float)rand()/(float)getrandmax();
            $solusi[$i] = number_format($random, 2);
        }
        
        //Hitung Error
        $prediksi = 0;
        $total_kasus = 0;
        $error = 0;
        foreach ($dbd as $k => $v) {
            $total_kasus += $dbd[$k]->kasus;
            $prediksi += 
                    ($solusi[0] * pow(($dbd[$k]->ch), 2)) + ($solusi[1] * $dbd[$k]->ch) + 
                    ($solusi[2] * pow(($dbd[$k]->hh), 2)) + ($solusi[3] * $dbd[$k]->hh) + 
                    ($solusi[4] * pow(($dbd[$k]->abj), 2)) + ($solusi[5] * $dbd[$k]->abj) + 
                    ($solusi[6] * pow(($dbd[$k]->hi), 2)) + ($solusi[7] * $dbd[$k]->hi);
        }
        $error = pow($total_kasus - $prediksi, 2);
        
        //Penetapan Smin dan Emin
        $solusi_min = $solusi;
        $error_min = $error;        
        
        //Inisialisasi K dan T
        $K = $error_min * 10;
        $T = 0.8;
        
        for ($iterasi = 0; $iterasi < 50; $iterasi++){
            //Acak Solusi
            for ($i = 0; $i < 8; $i++) {
                $random = (float)rand()/(float)getrandmax();
                $solusi[$i] = number_format($random, 2);
            }

            //Hitung Error
            $prediksi = 0;
            $total_kasus = 0;
            $error = 0;
            foreach ($dbd as $k => $v) {
                $total_kasus += $dbd[$k]->kasus;
                $prediksi += ($solusi[0] * $dbd[$k]->ch) + ($solusi[1] * $dbd[$k]->hh) + ($solusi[2] * $dbd[$k]->abj) + ($solusi[3] * $dbd[$k]->hi);
            }
            $error = pow($total_kasus - $prediksi, 2);

            //Pengubahan Smin dan Emin
            $r = (float)rand()/(float)getrandmax();
            $exp = exp((-1 * ($error - $error_min)/($K * $T)));
            if($r < $exp){
//            if($error_min > $error){
                $solusi_min = $solusi;
                $error_min = $error;
                $prediksi_min = $prediksi;
            }

            //Pengubahan Temperature
            $coolingRate = 0.003;
            $T = $T * (1 - $coolingRate);
        }        

        $params['kasus'] = $total_kasus;
        $params['error_min'] = $error_min;
        $params['solusi_min'] = $solusi_min;
        $params['prediksi_min'] = $prediksi_min;
        
//        return $this->render('hitung', ['error_min' => $error_min, 'solusi_min' => $solusi_min]);
        return $this->render('hitung', $params);
    }        
}

function acakSolusi ($banyak){
    for ($i = 0; $i < $banyak; $i++) {
        $random = (float)rand()/(float)getrandmax();
        $solusi[$i] = number_format($random, 2);
    }
    return $solusi;
}

function hitungError($solusi, $dbd){
    $prediksi = 0;
    $total_kasus = 0;
    $error = 0;
    
    foreach ($dbd as $k => $v) {
        $total_kasus += $dbd[$k]->kasus;
        $prediksi += ($solusi[0] * $dbd[$k]->ch) + ($solusi[1] * $dbd[$k]->hh) + ($solusi[2] * $dbd[$k]->abj) + ($solusi[3] * $dbd[$k]->hi);
    }
    $error = sqrt((pow($total_kasus - $prediksi, 2))/sizeof($dbd));
    
    $hitungError['error'] = $error;
    $hitungError['prediksi'] = $prediksi;
    $hitungError['total_kasus'] = $total_kasus;
    return $hitungError;
}