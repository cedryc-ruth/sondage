<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Responses Controller
 *
 * @property \App\Model\Table\ResponsesTable $Responses
 *
 * @method \App\Model\Entity\Response[] paginate($object = null, array $settings = [])
 */
class ResponsesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['vote']);
    }

    public function isAuthorized($user) {
        $user = $this->Auth->user();
        if(empty($user)) return false;
        
        if($this->request->getParam('action')=='addAll') {
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
        $responses = $this->paginate($this->Responses);

        $this->set(compact('responses'));
        $this->set('_serialize', ['responses']);
    }

    /**
     * View method
     *
     * @param string|null $id Response id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $response = $this->Responses->get($id, [
            'contain' => ['Surveys']
        ]);

        $this->set('response', $response);
        $this->set('_serialize', ['response']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $response = $this->Responses->newEntity();
        if ($this->request->is('post')) {
            $response = $this->Responses->patchEntity($response, $this->request->getData());
            if ($this->Responses->save($response)) {
                $this->Flash->success(__('The response has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The response could not be saved. Please, try again.'));
        }
        $surveys = $this->Responses->Surveys->find('list', ['limit' => 200]);
        $this->set(compact('response', 'surveys'));
        $this->set('_serialize', ['response']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Response id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $response = $this->Responses->get($id, [
            'contain' => ['Surveys']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $response = $this->Responses->patchEntity($response, $this->request->getData());
            if ($this->Responses->save($response)) {
                $this->Flash->success(__('The response has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The response could not be saved. Please, try again.'));
        }
        $surveys = $this->Responses->Surveys->find('list', ['limit' => 200]);
        $this->set(compact('response', 'surveys'));
        $this->set('_serialize', ['response']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Response id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $response = $this->Responses->get($id);
        if ($this->Responses->delete($response)) {
            $this->Flash->success(__('The response has been deleted.'));
        } else {
            $this->Flash->error(__('The response could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    /**
     * AddAll method
     *
     * @param array $responses Le tableau de string (title des Responses à persister)
     * @return array Redirects to SurveysController on successful add 
     *          with array of Response's ids, empty array otherwise.
     */
    public function addAll($responses){
        $responses = explode('|',$responses);
        
        foreach ($responses as $title) {
            $result = [];
            
            $response = $this->Responses->newEntity();
            $response->title = $title;
            $response->count = 0;
            //debug($response);
            
            $cpt = 0;
            $response = $this->Responses->save($response);
            if($response) {
                $cpt++;
                $result[] = $response->id;
            }
            
            if($cpt==sizeof($responses)) {
                $this->Flash->success(__('All the responses have been saved.'));
            } else {
                $this->Flash->error(__((sizeof($responses)-$cpt).' response(s) could not be saved.'));
            }
        }
        return $this->redirect([
            'controller' => 'Surveys',
            'action' => 'add',
            'param' => implode('|',$result)]);
    }
         
    public function vote($id) {
        $response = $this->Responses->get($id);
        
        $response->count++;
        
        if($this->Responses->save($response)) {
            $this->Flash->success(__("Votre vote a bien été enregistré."));
        } else {
            $this->Flash->error(__("Une erreur s'est produite!"));
        }
        
        $this->redirect(['action'=>'index']);
    }
}
