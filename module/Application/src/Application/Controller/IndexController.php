<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Db\Sql\Sql;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        if (!$this->getServiceLocator()
                 ->get('AuthService')->hasIdentity()){
            return $this->redirect()->toRoute('login');
        }

        return new ViewModel();
    }

    public function saveNoteAction()
    {
        if(!$this->getServiceLocator()->get('AuthService')->hasIdentity()) {
            return;
        }

        if (isset($_POST['note'])) {
            $userEmail = $this->getServiceLocator()
                 ->get('AuthService')->getIdentity();
            
            //get the inserted note and sanitize it
            $note = htmlspecialchars($name = $this->getRequest()->getPost('note'));

            $sm = $this->getServiceLocator();
            $adapter = $this->adapter = $sm->get('Zend\Db\Adapter\Adapter');
            
            $sql = new Sql($adapter);
            $insert = $sql->insert('notes');
            $newData = array(
                'note'=> $note,
                'userEmail'=> $userEmail
            );
            $insert->values($newData);
            $selectString = $sql->buildSqlString($insert);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);

            exit;
        }
    }

    public function loadNotesAction()
    {
        if(!$this->getServiceLocator()->get('AuthService')->hasIdentity()) {
            return;
        }

        if (isset($_POST['limit'])) {
            $user_email = $this->getServiceLocator()
                    ->get('AuthService')->getIdentity();

            $limit = htmlspecialchars($name = $this->getRequest()->getPost('limit'));

            $sm = $this->getServiceLocator();
            $adapter = $this->adapter = $sm->get('Zend\Db\Adapter\Adapter');

            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('notes');
            $select->where(array('userEmail' => $user_email));
            $select->order('id DESC');
            $select->limit($limit);

            $selectString = $sql->buildSqlString($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);

            return new JsonModel($results);
        }
    }
}
