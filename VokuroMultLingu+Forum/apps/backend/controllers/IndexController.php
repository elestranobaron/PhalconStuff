<?php
namespace Test\Backend\Controllers;

/**
 * Display the default index page.
 */
class IndexController extends ControllerBase
{

    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {
	$language = $this->session->get('language');
        $this->view->setVar('logged_in', is_array($this->auth->getIdentity()));
        $this->view->setTemplateBefore('public');//public'); // avec index bonne page mais sans css
    }
    public function setLanguageAction($language = '')
    {
	if ($language == 'en' || $language == 'es') {
		$this->session->set('language', $language);
	}
	//Go to the last place
	$referer = $this->request->getHTTPReferer();
	if (strpos($referer,
		$this->request->getHttpHost()."/") !== false) {
			return $this->response->setHeader("Location", $referer);
	} else {
			return $this->dispatcher->forward(array('controller' =>
		'index', 'action' => 'index')); 
	} 
    }
}
