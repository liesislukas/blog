<?php

namespace Phalconvn\Controllers;

use Phalcon\Tag,
	Phalcon\Mvc\Model\Criteria,
	Phalcon\Paginator\Adapter\Model as Paginator;

use Phalconvn\Forms\ProfilesForm,
	Phalconvn\Models\Profiles;

/**
 * Phalconvn\Controllers\ProfilesController
 *
 * CRUD to manage profiles
 */
class ProfilesController extends ControllerBase
{

    public function initialize()
    {
    	$this->view->setTemplateBefore('private');
	}

	/**
	 * Default action, shows the search form
	 */
	public function indexAction()
	{
		$this->persistent->conditions = null;
		$this->view->form = new ProfilesForm();
	}

	/**
	 * Searches for profiles
	 */
	public function searchAction()
	{
		$numberPage = 1;
		if ($this->request->isPost()) {
			$query = Criteria::fromInput($this->di, 'Phalconvn\Models\Profiles', $this->request->getPost());
			$this->persistent->searchParams = $query->getParams();
		} else {
			$numberPage = $this->request->getQuery("page", "int");
		}

		$parameters = array();
		if ($this->persistent->searchParams) {
			$parameters = $this->persistent->searchParams;
		}

		$profiles = Profiles::find($parameters);
		if (count($profiles) == 0) {

			$this->flash->notice("The search did not find any profiles");

			return $this->dispatcher->forward(array(
				"action" => "index"
			));
		}

		$paginator = new Paginator(array(
			"data" => $profiles,
			"limit" => 10,
			"page" => $numberPage
		));

		$this->view->page = $paginator->getPaginate();
	}

	/**
	 * Creates a new Profile
	 *
	 */
	public function createAction()
	{
		if ($this->request->isPost()) {

			$profile = new Profiles();

			$profile->assign(array(
				'name' => $this->request->getPost('name', 'striptags'),
				'active' => $this->request->getPost('active')
			));

			if (!$profile->save()) {
				$this->flash->error($profile->getMessages());
			} else {
				$this->flash->success("Profile was created successfully");
			}

			Tag::resetInput();
		}

		$this->view->form = new ProfilesForm(null);
	}

	/**
	 * Edits an existing Profile
	 *
	 * @param int $id
	 */
	public function editAction($id)
	{

		$profile = Profiles::findFirstById($id);
		if (!$profile) {
			$this->flash->error("Profile was not found");
			return $this->dispatcher->forward(array('action' => 'index'));
		}

		if ($this->request->isPost()) {

			$profile->assign(array(
				'name' => $this->request->getPost('name', 'striptags'),
				'active' => $this->request->getPost('active')
			));

			if (!$profile->save()) {
				$this->flash->error($profile->getMessages());
			} else {
				$this->flash->success("Profile was updated successfully");
			}

			Tag::resetInput();
		}

		$this->view->form = new ProfilesForm($profile, array('edit' => true));

		$this->view->profile = $profile;
	}

	/**
	 * Deletes a Profile
	 *
	 * @param int $id
	 */
	public function deleteAction($id)
	{

		$profile = Profiles::findFirstById($id);
		if (!$profile) {

			$this->flash->error("Profile was not found");

			return $this->dispatcher->forward(array('action' => 'index'));
		}

		if (!$profile->delete()) {
			$this->flash->error($profile->getMessages());
		} else {
			$this->flash->success("Profile was deleted");
		}

		return $this->dispatcher->forward(array('action' => 'index'));
	}

}
