<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use App\Model\Entity\Survey;

/**
 * Surveys Controller
 *
 * @property \App\Model\Table\SurveysTable $Surveys
 *
 * @method \App\Model\Entity\Survey[] paginate($object = null, array $settings = [])
 */
class SurveysController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow([
            'getSurveysByUserId',
            'search',
            'searchSurveysByGroup',
        ]);
    }
    
    public function isAuthorized($user) {
        $user = $this->Auth->user();
        if(empty($user)) return false;
        
        if($this->request->getParam('action')=='add') {
            return true;
        }
    }
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users','Responses'],
            'limit' => 3
        ];
        $surveys = $this->paginate($this->Surveys);

        foreach($surveys as $survey) {
            $survey->total = $survey->computePourcentage();
        }
        
        $this->set(compact('surveys'));
        $this->set('_serialize', ['surveys']);
    }

    /**
     * View method
     *
     * @param string|null $id Survey id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $survey = $this->Surveys->get($id, [
            'contain' => ['Users', 'Responses']
        ]);

        $survey->total = $survey->computePourcentage();
        
        $this->set('survey', $survey);
        $this->set('_serialize', ['survey']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {/*
        if($newResponses = $this->request->data['new_responses']) {
            $newResponses = explode(',', $newResponses);
            $newResponses = array_map('trim',$newResponses);
            
            $responses = TableRegistry::get('responses');
            $responses->save($entity);
            debug();
        }
        
        die;*/
        $survey = $this->Surveys->newEntity();
        if ($this->request->is('post')) {
            $survey = $this->Surveys->patchEntity($survey, $this->request->getData());
            if ($this->Surveys->save($survey)) {
                $this->Flash->success(__('The survey has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The survey could not be saved. Please, try again.'));
        }
        $users = $this->Surveys->Users->find('list', ['limit' => 200]);
        $responses = $this->Surveys->Responses->find('list', ['limit' => 200]);
        $this->set(compact('survey', 'users', 'responses'));
        $this->set('_serialize', ['survey']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Survey id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $survey = $this->Surveys->get($id, [
            'contain' => ['Responses']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $survey = $this->Surveys->patchEntity($survey, $this->request->getData());
            if ($this->Surveys->save($survey)) {
                $this->Flash->success(__('The survey has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The survey could not be saved. Please, try again.'));
        }
        $users = $this->Surveys->Users->find('list', ['limit' => 200]);
        $responses = $this->Surveys->Responses->find('list', ['limit' => 200]);
        $this->set(compact('survey', 'users', 'responses'));
        $this->set('_serialize', ['survey']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Survey id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $survey = $this->Surveys->get($id);
        if ($this->Surveys->delete($survey)) {
            $this->Flash->success(__('The survey has been deleted.'));
        } else {
            $this->Flash->error(__('The survey could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    public function getSurveysByUserId($id = null)
    {
        if($id==$this->Auth->user('id')) {
            $this->paginate = [
                'contain' => ['Users']
            ];

            //Récupérer les sondages du user donné
            $query = $this->Surveys->find('all')
                    ->where(['user_id'=>$id])
                    ->contain(['Responses']);

            //Envoyer vers la vue
            $this->set('surveys',$this->paginate($query));
        } else {
            $this->Flash->error("Vous n'êtes pas autorisé à afficher d'autres sondages.");
            return $this->redirect(['action' => 'index']);
        }
    }
    
    /**
     * Method search 
     * Recherche et affiche les sondages sur base d'un mot-clé
     * 
     * @return \Cake\Http\Response
     */
    public function search()
    {
        //Récupérer le mot-clé tapé dans le formulaire de recherche
        $keyword = $this->request->getQuery('keyword');

        $this->paginate = [
            'contain' => ['Users','Responses'],
            'limit' => 3,
            'finder' => [
                'SurveysByKeyword'=>['keyword'=>$keyword]
            ]
        ];
                
        //Préparer et exécuter  la requête
        $surveys = $this->paginate($this->Surveys);
        
        //Solution 2:
        /* $surveys = $this->paginate($this->Surveys->find('SurveysByKeyword',[
         *      'keyword'=>$keyword
         * ]));
        */
        
        //Envoyer les données à la vue pour un template spécifique
        $this->set(compact('surveys'));
        $this->render('index');
    }
    
    public function searchSurveysByGroup($groupId)
    {
        if($groupId != $this->Auth->user('group_id')) {
            $this->Flash->error(__('Vous ne pouvez voir que les sondages de votre groupe!'));
            
            return $this->redirect($this->referer());
        }
        
        $this->paginate = [
            'contain' => ['Users.Groups','Responses'],
        ];
        
        //Extraire les données
        $query = $this->Surveys->find()
                ->where(['Users.group_id'=>$groupId]);
        
        $surveys = $this->paginate($query);
        
        foreach($surveys as $survey) {
            $survey->total = $survey->computePourcentage();
        }
        
        $group = $this->Surveys->Users->Groups
                ->find()->where(['id'=>$groupId])->first();
        
        //Envoyer les données vers la vue
        $this->set(compact('surveys'));
        $this->set(compact('group'));
    }
}
