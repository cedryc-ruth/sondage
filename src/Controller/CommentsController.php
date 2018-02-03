<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Comments Controller
 *
 * @property \App\Model\Table\CommentsTable $Comments
 *
 * @method \App\Model\Entity\Comment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CommentsController extends AppController
{

    /**
     * isAuthorized Méthode de gestion d'accès par fonctionnalité
     * 
     * @param array $user L'utilisateur authentifié
     * @return boolean true si l'utilisateur est authentifié 
     *          et qu'il a créé au moins 2 sondages, false sinon.
     */
    public function isAuthorized($user) {
        $user = $this->Auth->user();
        if(empty($user)) return false;
        
        if($this->request->getParam('action')=='getComments') {
            //L'utilisateur doit avoir créé au moins 2 sondages
            if($this->Comments->Surveys->findByUserId($user['id'])->count()<2) {
                $this->Flash->error('Pour afficher les commentaires, vous devez vous connecter et avoir créé au moins 2 sondages.');
                
                return false;
            }
            return true;
        }
    }
    
    /**
     * GetComments method
     *
     * @return \Cake\Http\Response|void
     */
    public function getComments($id_survey)
    {
        //Configuration de la pagination
        $this->paginate = [
            'contain' => ['Surveys', 'Users']
        ];
        
        //Récupération et pagination des commentaire du sondage
        $comments = $this->paginate($this->Comments->findBySurveyId($id_survey));
        
        //Récupération du sondage
        $survey = $this->Comments->Surveys->findById($id_survey)->first();
        
        //Envoi des données à la vue
        $this->set(compact('comments'));
        $this->set(compact('survey'));
        
        //Affichage dans le template "comment_liste.ctp"
        $this->render('comment_liste');
    }
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Surveys', 'Users']
        ];
        $comments = $this->paginate($this->Comments);

        $this->set(compact('comments'));
    }

    /**
     * View method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $comment = $this->Comments->get($id, [
            'contain' => ['Surveys', 'Users']
        ]);

        $this->set('comment', $comment);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $comment = $this->Comments->newEntity();
        if ($this->request->is('post')) {
            $comment = $this->Comments->patchEntity($comment, $this->request->getData());
            if ($this->Comments->save($comment)) {
                $this->Flash->success(__('The comment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The comment could not be saved. Please, try again.'));
        }
        $surveys = $this->Comments->Surveys->find('list', ['limit' => 200]);
        $users = $this->Comments->Users->find('list', ['limit' => 200]);
        $this->set(compact('comment', 'surveys', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $comment = $this->Comments->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $comment = $this->Comments->patchEntity($comment, $this->request->getData());
            if ($this->Comments->save($comment)) {
                $this->Flash->success(__('The comment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The comment could not be saved. Please, try again.'));
        }
        $surveys = $this->Comments->Surveys->find('list', ['limit' => 200]);
        $users = $this->Comments->Users->find('list', ['limit' => 200]);
        $this->set(compact('comment', 'surveys', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Comment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $comment = $this->Comments->get($id);
        if ($this->Comments->delete($comment)) {
            $this->Flash->success(__('The comment has been deleted.'));
        } else {
            $this->Flash->error(__('The comment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
