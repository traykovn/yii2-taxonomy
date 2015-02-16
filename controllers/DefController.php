<?php

namespace nkostadinov\taxonomy\controllers;

use Yii;
use nkostadinov\taxonomy\models\TaxonomyDef;
use nkostadinov\taxonomy\models\TaxonomyDefSearch;
use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DefController implements the CRUD actions for TaxonomyDef model.
 */
class DefController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all TaxonomyDef models.
     * @return mixed
     */
    public function actionIndex()
    {
        if(!$this->getComponent()->isInstalled())
            $this->redirect($this->module->id . '/' . $this->id . '/install');

        $searchModel = new TaxonomyDefSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TaxonomyDef model.
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
     * Creates a new TaxonomyDef model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TaxonomyDef();
        $definitions = $this->getComponent()->getDefinitions();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'definitions' => $definitions,
            ]);
        }
    }

    /**
     * Updates an existing TaxonomyDef model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $definitions = $this->getComponent()->getDefinitions();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'definitions' => $definitions,
            ]);
        }
    }

    /**
     * Deletes an existing TaxonomyDef model.
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
     * Finds the TaxonomyDef model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TaxonomyDef the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TaxonomyDef::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function getComponent()
    {
        if(\Yii::$app->has($this->module->component))
            return \Yii::$app->{$this->module->component};
        else
            throw new InvalidConfigException("Cannot find taxonomy component({$this->module->component})");
    }

    public function actionInstall()
    {
        if(!$this->getComponent()->isInstalled() and \Yii::$app->request->isPost) {
            //start installation
            if($this->getComponent()) {
                $this->getComponent()->install();

                $this->redirect($this->module->id . '/' . $this->id . '/index');
            } else
                throw new InvalidConfigException("Cannot find taxonomy component({$this->module->component})");
        }
        return $this->render('install');
    }

    public function actionInstallterm($term)
    {
        $term = $this->getComponent()->getTerm($term);
        $term->install();
    }
}