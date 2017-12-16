<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[] paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['logout','signin','profil']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
        $this->set('_serialize', ['users']);
    }

    /**
     * Permet d'afficher les données de l'utilisateur.
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if($id!=$this->Auth->user('id')) {
            $this->redirect($this->referer());
        }
        
        $tableSurveys = TableRegistry::get('Surveys');
        $query = $tableSurveys->find('SurveysByUserId',['id'=>$id]);
        $nbSurveys = $query->count();
        $this->set('nbSurveys', $nbSurveys);
        
        $userEntity = $this->Users->get($id, [
            'contain' => []
        ]);
        
        //var_dump($this->request->query);
        $showUpdateForm = false;
        if(isset($this->request->query['edit'])) {
            $showUpdateForm = true;
        }
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $newPass = $this->request->getData('newPassword');
            $confirmPass = $this->request->getData('confirmPassword');

            if($newPass==$confirmPass) {
                $userEntity = $this->Users->patchEntity($userEntity, $this->request->getData());
                if(!empty($newPass)) {
                    $userEntity->password = $newPass;
                }
                
                //var_dump($userEntity);die;
                if ($this->Users->save($userEntity)) {
                    $this->Flash->success(__('The user has been saved.'));

                    return $this->redirect(['action' => 'view', $id]);
                }
                $this->Flash->error(__('The user could not be saved. Please, try again.'));
            } else {
                $this->Flash->error(__('Mots de passes différents!'));
            }
        }
        
        $this->set('userEntity', $userEntity);
        $this->set('showUpdateForm', $showUpdateForm);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
    public function login() {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error('Votre identifiant ou votre mot de passe est incorrect.');
        }
    }
    
    public function logout() {
        $this->Flash->success('Vous avez été déconnecté.');
        return $this->redirect($this->Auth->logout());
    }
    
    /**
     * Permet d'inscrire un nouvel utilisateur.
     * 
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function signin($user = null) {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $email = $this->request->getData('email');
            $confirmEmail = $this->request->getData('confirmEmail');
            
            if($email==$confirmEmail) {
                $otherUser = $this->Users->findByEmail($email)->first();
                if(empty($otherUser)) {

                    $user = $this->Users->patchEntity($user, $this->request->getData());
//                    debug($user);die;
                    
                    if ($this->Users->save($user)) {
                        $this->Flash->success(__('The user has been saved.'));

                        return $this->redirect(['action' => 'index']);
                    }
                } else {
                    $this->Flash->error(__('This email is already taken!'));
                }
            } else {
                $this->Flash->error(__('The two emails must be identical!'));
            }
            
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);        
        
        $this->render('inscription');
    }
    
}
