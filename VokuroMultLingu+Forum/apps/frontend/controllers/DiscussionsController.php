<?php

/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

namespace Phosphorum\Controllers;

use Phosphorum\Models\Posts;
use Phosphorum\Models\PostsViews;
use Phosphorum\Models\PostsReplies;
use Phosphorum\Models\PostsBounties;
use Phosphorum\Models\PostsHistory;
use Phosphorum\Models\PostsVotes;
use Phosphorum\Models\PostsSubscribers;
use Phosphorum\Models\Categories;
use Phosphorum\Models\Activities;
use Phosphorum\Models\ActivityNotifications;
use Phosphorum\Models\IrcLog;
use Phosphorum\Models\Users;
use Phosphorum\Models\Karma;
use Phosphorum\Models\TopicTracking;

use Phosphorum\Utils\Slug;
use Phosphorum\Search\Indexer;

use Phalcon\Http\Response;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;
use Phosphorum\Forms\ProfilesForm; // Ajout pour creation de categories manuelle
use Phalcon\Tag;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Paginator\Adapter\Model as Paginator;
/* Ligne 967 gestion des categories*/
/**
 * Class DiscussionsController
 *
 * @package Phosphorum\Controllers
 */
class DiscussionsController extends ControllerBase
{

    const POSTS_IN_PAGE = 40;

    /**
     * This initializes the timezone in each request
     */
    public function initialize()
    {
        $timezone = $this->session->get('identity-timezone');
        if ($timezone) {
            date_default_timezone_set($timezone);
        }
        $this->view->limitPost  = self::POSTS_IN_PAGE;
    }

    /**
     * This method prepares the queries to be executed in each list of posts
     * The returned builders are used as base in the search, tagged list and index lists
     */
    protected function prepareQueries($joinReply = false, $issub = false)
    {

        /** @var \Phalcon\Mvc\Model\Query\BuilderInterface $itemBuilder */
//        $itemBuilder2 = $this
  //          ->modelsManager
    //        ->createBuilder()
    //        ->from(array('c' => 'Phosphorum\Models\Categories')) //Ajout pour les categories
     //       ->orderBy('c.sub DESC');
//	if (!$issub) {
        $itemBuilder = $this
            ->modelsManager
            ->createBuilder()
//            ->from(array('c' => 'Phosphorum\Models\Categories')) //Ajout pour les categories
            ->from(array('p' => 'Phosphorum\Models\Posts'))
            ->orderBy('p.sticked DESC, p.created_at DESC');

        if ($joinReply) {
            $itemBuilder
                ->groupBy("p.id")
                ->join('Phosphorum\Models\PostsReplies', "r.posts_id = p.id", 'r');
        }

        $totalBuilder = clone $itemBuilder;

        $itemBuilder
            ->columns(array('p.*'))
            ->limit(self::POSTS_IN_PAGE);

        $totalBuilder
            ->columns('COUNT(*) AS count');

        return array($itemBuilder, $totalBuilder);
//	}
//	else {

//	}
    }

    /**
     * Shows latest posts using an order clause
     */
    public function indexAction($order = null, $offset = 0)
    {

        /** @var \Phalcon\Mvc\Model\Query\BuilderInterface $itemBuilder */
        /** @var \Phalcon\Mvc\Model\Query\BuilderInterface $totalBuilder */

        if ($order == "answers" || $order == "answerst") {
		if ($order == "answers")
            list($itemBuilder, $totalBuilder) = $this->prepareQueries(true);
		else
	    list($itemBuilder, $totalBuilder) = $this->prepareQueries(false, true);
        } else {
            list($itemBuilder, $totalBuilder) = $this->prepareQueries();
        }

        /**
         * Create the conditions according to the parameter order
         */
        $userId = $this->session->get('identity');
        $this->view->logged = $userId;
        if ($userId != '') {
            $ur = TopicTracking::findFirst("user_id='".$userId."'");
            if ($ur === false) {
                $this->view->readposts = array();
            } else {
                $this->view->readposts = explode(",", $ur->topic_id);
            }
        }

        $params = null;

        switch ($order) {

            case 'hot':
                $this->tag->setTitle('Hot Discussions');
                $itemBuilder->orderBy('p.modified_at DESC');
                break;

            case 'my':
                $this->tag->setTitle('My Discussions');
                if ($userId) {
                    $params       = array($userId);
                    $myConditions = 'p.users_id = ?0';
                    $itemBuilder->where($myConditions);
                    $totalBuilder->where($myConditions);
                }
                break;

            case 'unanswered':
                $this->tag->setTitle('Unanswered Discussions');
                $unansweredConditions = 'p.number_replies = 0 AND p.accepted_answer <> "Y"';
                $itemBuilder->where($unansweredConditions);
                $totalBuilder->where($unansweredConditions);
                break;

            case 'answers':
                $this->tag->setTitle('My Answers');
                if ($userId) {
                    $params            = array($userId);
                    $answersConditions = 'r.users_id = ?0';
                    $itemBuilder->where($answersConditions);
                    $totalBuilder->where($answersConditions);
                }
		break ; // j'ai ajoute ce break parce que answers n'est plus la derniere condition.
            case 'answerst':
/////////////////////		$gotsub = Categories::find("sub='".$category->name."'");
                $this->tag->setTitle('Sub');
                $catConditions = "p.categories_id='".$category->id."'";
                $itemBuilder->where($catConditions);
                $totalBuilder->where($catConditions);
/////////////////////////		$this->view->sub;
//		$cond = "c.sub";
//		$itemBuilder->where($cond);
//		if ($order == 'answerst') // ca semble bizarre mais c oblige ...
//			return ;
//		return ;
           /*     if ($userId) {
                    $params            = array($userId);
                    $answersConditions = 'r.users_id = ?0';
                    $itemBuilder->where($answersConditions);
                    $totalBuilder->where($answersConditions);
                }*/
                break;

            default:
                $this->tag->setTitle('Discussions');
        }
//	if ($order != 'answerst') {
        $notDeleteConditions = 'p.deleted = 0';
        $itemBuilder->andWhere($notDeleteConditions);
        $totalBuilder->andWhere($notDeleteConditions);

        if ($offset > 0) {
            $itemBuilder->offset((int)$offset);
        }

        $this->view->posts      = $itemBuilder->getQuery()->execute($params);
        $this->view->totalPosts = $totalBuilder->getQuery()->setUniqueRow(true)->execute($params);

        if (!$order) {
            $order = 'new';
        }

        $this->view->currentOrder = $order;
        $this->view->offset       = $offset;
        $this->view->paginatorUri = 'discussions/' . $order;
        $this->view->canonical    = '';
//	}
//	else {
////////////////////////	$this->view->sub;
//	}
    }
//    public function beforecategoryAction($categoryId, $slug, $offset = 0)
//    {
//print("toto")	;
//    }
    /**
     * Shows latest posts by category
     */
    public function categoryAction($categoryId, $slug, $offset = 0)
    {
        $this->tag->setTitle('Discussions');
//	$subway = 0;
//	if ($category = Categories::findFirst("sub='".$category->name."'")) {
//		$subway = 1;
	//	$this->beforecategoryAction($category->id, $category->slug, $offset);
	//	return ;
//	}

        $userId = $this->session->get('identity');
        if ($userId != '') {
            $ur = TopicTracking::findFirst("user_id='".$userId."'");
            $this->view->readposts = explode(",", $ur->topic_id);
        }

        $category = Categories::findFirstById($categoryId);
        if (!$category) {
            $this->flashSession->notice('The category doesn\'t exist');
            return $this->response->redirect();
        }

	if (!$this->view->gotsub = Categories::findFirst("sub='".$category->name."'")) { // Findirst au lieu de find monter bien la categorie uniquement si elle existe
        /** @var \Phalcon\Mvc\Model\Query\BuilderInterface $itemBuilder */
        /** @var \Phalcon\Mvc\Model\Query\BuilderInterface $totalBuilder */
        list($itemBuilder, $totalBuilder) = $this->prepareQueries();

        $totalBuilder->where('p.categories_id = ?0 AND p.deleted = 0');

        $posts = $itemBuilder
            ->where('p.categories_id = ?0 AND p.deleted = 0')
            ->orderBy('p.created_at DESC')
            ->offset((int)$offset)
            ->getQuery()
            ->execute(array($categoryId));

        if (!count($posts)) {
            $this->flashSession->notice('There are no posts in category: ' . $category->name);
            return $this->response->redirect();
        }

        $totalPosts = $totalBuilder->getQuery()->setUniqueRow(true)->execute(array($categoryId));

//	if (1)
//		$this->view->gotsub = true;
        $this->view->posts        = $posts;
        $this->view->totalPosts   = $totalPosts;
        $this->view->currentOrder = null;
        $this->view->offset       = (int)$offset;
        $this->view->paginatorUri = 'category/' . $category->id . '/' . $category->slug;
        $this->view->logged = $this->session->get('identity');
	}
	else {
        $this->tag->setTitle('Forum');
        $userId = $this->session->get('identity');

        foreach (Categories::find() as $category) {
            if (count(\Phosphorum\Models\Posts::find("categories_id=".$category->id)) > 0) {
                $last_author[$category->id] = $this
                ->modelsManager
                ->createBuilder()
                ->from(array('p' => 'Phosphorum\Models\Posts'))
                ->where('p.categories_id = "'.$category->id.'"')
                ->join('Phosphorum\Models\Users', "u.id = p.users_id", 'u')
                ->columns(array('p.users_id as users_id','u.name as name_user','p.title as post1_title','p.slug as post1_slug','p.id as post1_id'))
                ->orderBy('p.id DESC')
                ->limit(1)
                ->getQuery()
                ->execute();
            } else {
                $last_author[$category->id] = 0;
            }

          //SQL

            $sql[$category->id] = "SELECT * FROM `posts` JOIN topic_tracking ON topic_tracking.topic_id WHERE concat(posts.id) AND NOT(FIND_IN_SET(posts.id, topic_tracking.topic_id)) AND categories_id = '{$category->id}' AND topic_tracking.user_id = '{$userId}'";
            $not_read[$category->id] = $this->db->query($sql[$category->id]);

        }

        if ($userId !='') {
            $check_topic = new TopicTracking();
            $check_topic->user_id = ''.$this->session->get('identity').'';
            $check_topic->topic_id = '9999999';
            $check_topic->create();
        }

          $this->view->last_author = $last_author;
          $this->view->not_read = $not_read;
          $this->view->logged = $this->session->get('identity');
        $category = Categories::findFirstById($categoryId);				//
        if (!$category) {							// Rangement Cat
            $this->flashSession->notice('The category doesn\'t exist');			//
            return $this->response->redirect();
        }
	//////////////////////	print($category->name);
          $this->view->categoriessub = Categories::find("sub='".$category->name."'");
	}
/////////////////	if ($category = Categories::findFirst("sub='".$category->name."'"))
////////////////		$this->categoryAction($category->id, $category->slug, $offset);
    }

    protected function checkTokenPost()
    {
        if (!$this->security->checkToken()) {
            $this->flashSession->error('Token error. This might be CSRF attack.');
            return false;
        }
        return true;
    }

    /**
     * This shows the create post form and also store the related post
     */
    public function createAction()
    {

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        $this->tag->setTitle('Start a Discussion');

        if ($this->request->isPost()) {
            if (!$this->checkTokenPost()) {
                return $this->response->redirect();
            }

            $title = $this->request->getPost('title', 'trim');

            /** @var Users $user */
            $user = Users::findFirstById($usersId);
            $user->increaseKarma(Karma::ADD_NEW_POST);
            $user->save();

            $post                = new Posts();
            $post->users_id      = $usersId;
            $post->categories_id = $this->request->getPost('categoryId');
            $post->title         = $title;
            $post->slug          = Slug::generate($title);
            $post->content       = $this->request->getPost('content');

            if ($post->save()) {
                return $this->response->redirect('discussion/' . $post->id . '/' . $post->slug);
            }

            foreach ($post->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->view->firstTime = false;

        } else {

            $this->view->firstTime = Posts::countByUsersId($usersId) == 0;
        }

        $parameters = array(
            'order' => 'name'
        );

        $this->view->categories = Categories::find($parameters);
    }

    /**
     * This shows the create post form and also store the related post
     */
    public function editAction($id)
    {

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        /**
         * Find the post using get
         */
        $parameters = array(
            "id = ?0 AND (users_id = ?1 OR 'Y' = ?2)",
            "bind" => array($id, $usersId, $this->session->get('identity-moderator'))
        );
        $post = Posts::findFirst($parameters);

        if (!$post) {
            $this->flashSession->error('The discussion does not exist');
            return $this->response->redirect();
        }

        if ($this->request->isPost()) {
            if (!$this->checkTokenPost()) {
                return $this->response->redirect();
            }

            $title   = $this->request->getPost('title', 'trim');
            $content = $this->request->getPost('content');

            $post->categories_id = $this->request->getPost('categoryId');
            $post->title         = $title;
            $post->slug          = Slug::generate($title);
            $post->content       = $content;
            $post->edited_at     = time();

            $usersId = $this->session->get('identity');
            if ($post->users_id != $usersId) {

                /** @var Users $user */
                $user = Users::findFirstById($usersId);
                if ($user) {
                    $user->increaseKarma(Karma::MODERATE_POST);
                    $user->save();
                }
            }

            if ($post->save()) {
                return $this->response->redirect('discussion/' . $post->id . '/' . $post->slug);
            }

            foreach ($post->getMessages() as $message) {
                $this->flash->error($message);
            }

        } else {

            $this->tag->displayTo('id', $post->id);
            $this->tag->displayTo('title', $post->title);
            $this->tag->displayTo('content', $post->content);
            $this->tag->displayTo('categoryId', $post->categories_id);
        }

        $this->tag->setTitle('Edit Discussion: ' . $this->escaper->escapeHtml($post->title));

        $parametersCategory = array(
            'order' => 'name'
        );

        $this->view->categories = Categories::find($parametersCategory);

        $this->view->post = $post;
    }

    protected function checkTokenGet()
    {
        $csrfKey = $this->session->get('$PHALCON/CSRF/KEY$');
        $csrfToken = $this->request->getQuery($csrfKey, null, 'dummy');
        if (!$this->security->checkToken($csrfKey, $csrfToken)) {
            $this->flashSession->error('Token error. This might be CSRF attack.');
            return false;
        }
        return true;
    }

    /**
     * This shows the create post form and also store the related post
     */
    public function deleteAction($id)
    {
        if (!$this->checkTokenGet()) {
            return $this->response->redirect();
        }

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        /**
         * Find the post using get
         */
        $parameters = array(
            "id = ?0 AND (users_id = ?1 OR 'Y' = ?2)",
            "bind" => array($id, $usersId, $this->session->get('identity-moderator'))
        );

        $post = Posts::findFirst($parameters);
        if (!$post) {
            $this->flashSession->error('The discussion does not exist');
            return $this->response->redirect();
        }

        if ($post->sticked == 'Y') {
            $this->flashSession->error('The discussion cannot be deleted because it\'s sticked');
            return $this->response->redirect();
        }

        $post->deleted = 1;
        if ($post->save()) {

            $usersId = $this->session->get('identity');
            if ($post->users_id != $usersId) {

                /** @var Users $user */
                $user = Users::findFirstById($usersId);
                if ($user) {
                    if ($user->moderator == 'Y') {
                        $user->increaseKarma(Karma::MODERATE_DELETE_POST);
                        $user->save();
                    }
                }

                $user = $post->user;
                $user->decreaseKarma(Karma::DELETE_POST);
                $user->save();
            }

            $this->flashSession->success('Discussion was successfully deleted');
            return $this->response->redirect();
        }
    }

    /**
     * Subscribe to a post to receive e-mail notifications
     *
     * @param string $id
     */
    public function subscribeAction($id)
    {
        if (!$this->checkTokenGet()) {
            return $this->response->redirect();
        }

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        $post = Posts::findFirstById($id);
        if (!$post) {
            $this->flashSession->error('The discussion does not exist');
            return $this->response->redirect();
        }

        $subscription = PostsSubscribers::findFirst(array(
            'posts_id = ?0 AND users_id = ?1',
            'bind' => array($post->id, $usersId)
        ));
        if (!$subscription) {
            $subscription             = new PostsSubscribers();
            $subscription->posts_id   = $post->id;
            $subscription->users_id   = $usersId;
            $subscription->created_at = time();
            if ($subscription->save()) {
                $this->flashSession->notice('You are now subscribed to this post');
            }
        }

        return $this->response->redirect('discussion/' . $post->id . '/' . $post->slug);
    }

    /**
     * Unsubscribe from a post of receiving e-mail notifications
     *
     * @param string $id
     */
    public function unsubscribeAction($id)
    {
        if (!$this->checkTokenGet()) {
            return $this->response->redirect();
        }

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        $post = Posts::findFirstById($id);
        if (!$post) {
            $this->flashSession->error('The discussion does not exist');
            return $this->response->redirect();
        }

        $subscription = PostsSubscribers::findFirst(array(
            'posts_id = ?0 AND users_id = ?1',
            'bind' => array($post->id, $usersId)
        ));
        if ($subscription) {
            $this->flashSession->notice('You were successfully unsubscribed from this post');
            $subscription->delete();
        }

        return $this->response->redirect('discussion/' . $post->id . '/' . $post->slug);
    }

    /**
     * Displays a post and its comments
     *
     * @param $id
     * @param $slug
     *
     * @return \Phalcon\Http\ResponseInterface
     */
    public function viewAction($id, $slug)
    {
        $id = (int)$id;

        $usersId = $this->session->get('identity');

        #Check read / unread topic

        if ($usersId !='') {
            $check_topic = new TopicTracking();
            $check_topic->user_id = $usersId;
            $check_topic->topic_id = $id;
            if ($check_topic->create() == false) {
                $sql     = "UPDATE topic_tracking SET topic_id=IF(topic_id='',{$id}, CONCAT(topic_id,',{$id}')) WHERE user_id=:user_id AND NOT (FIND_IN_SET('{$id}', topic_id) OR FIND_IN_SET(' {$id}', topic_id));";
                $this->db->query($sql, array("user_id" => $usersId));
            } else {
            }
        }


        if (!$this->request->isPost()) {

            /**
             * Find the post using get
             */
            $post = Posts::findFirstById($id);
            if (!$post) {
                $this->flashSession->error('The discussion does not exist');
                return $this->response->redirect();
            }

            if ($post->deleted) {
                $this->flashSession->error('The discussion is deleted');
                return $this->response->redirect();
            }

            $ipAddress = $this->request->getClientAddress();

            $parameters = array(
                'posts_id = ?0 AND ipaddress = ?1',
                'bind' => array($id, $ipAddress)
            );
            $viewed = PostsViews::count($parameters);

            /**
             * A view is stored by ipaddress
             */
            if (!$viewed) {

                /**
                 * Increase the number of views in the post
                 */
                $post->number_views++;
                if ($post->users_id != $usersId) {

                    $post->user->increaseKarma(Karma::VISIT_ON_MY_POST);

                    if ($usersId > 0) {

                        /** @var Users $user */
                        $user = Users::findFirstById($usersId);
                        if ($user) {
                            if ($user->moderator == 'Y') {
                                $user->increaseKarma(Karma::MODERATE_VISIT_POST);
                            } else {
                                $user->increaseKarma(Karma::VISIT_POST);
                            }
                            $user->save();
                        }
                    }
                }

                $postView            = new PostsViews();
                $postView->post      = $post;
                $postView->ipaddress = $ipAddress;
                if (!$postView->save()) {
                    foreach ($postView->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                }
            }

            if (!$usersId) {

                /**
                 * Enable cache
                 */
                $this->view->cache(array('key' => 'post-' . $id));

                /**
                 * Check for a cache
                 */
                if ($this->viewCache->exists('post-' . $id)) {
                    return;
                }
            }

            /**
             * Generate cannonical meta
             */
            $this->view->canonical = 'discussion/' . $post->id . '/' . $post->slug;
            $this->view->author    = $post->user;

        } else {

            if (!$this->checkTokenPost()) {
                return $this->response->redirect();
            }

            if (!$usersId) {
                $this->flashSession->error('You must be logged in first to add a comment');
                return $this->response->redirect();
            }

            /**
             * Find the post using POST
             */
            $post = Posts::findFirstById($this->request->getPost('id'));
            if (!$post) {
                $this->flashSession->error('The discussion does not exist');
                return $this->response->redirect();
            }

            if ($post->deleted) {
                $this->flashSession->error('The discussion is deleted');
                return $this->response->redirect();
            }

            $content = $this->request->getPost('content', 'trim');
            if ($content) {

                /**
                 * Check if the question can have a bounty before add the reply
                 */
                $canHaveBounty = $post->canHaveBounty();

                $user = Users::findFirstById($usersId);
                if (!$user) {
                    $this->flashSession->error('You must be logged in first to add a comment');
                    return $this->response->redirect();
                }

                /**
                 * Only update the number of replies if the user that commented isn't the same that posted
                 */
                if ($post->users_id != $usersId) {

                    $post->number_replies++;
                    $post->modified_at = time();
                    $post->user->increaseKarma(Karma::SOMEONE_REPLIED_TO_MY_POST);

                    $user->increaseKarma(Karma::REPLY_ON_SOMEONE_ELSE_POST);
                    $user->save();
                }

                $postReply                 = new PostsReplies();
                $postReply->post           = $post;
                $postReply->in_reply_to_id = $this->request->getPost('reply-id', 'int');
                $postReply->users_id       = $usersId;
                $postReply->content        = $content;

                if ($postReply->save()) {

                    if ($post->users_id != $usersId && $canHaveBounty) {
                        $bounty                       = $post->getBounty();
                        $postBounty                   = new PostsBounties();
                        $postBounty->posts_id         = $post->id;
                        $postBounty->users_id         = $usersId;
                        $postBounty->posts_replies_id = $postReply->id;
                        $postBounty->points           = $bounty['value'];
                        if (!$postBounty->save()) {
                            foreach ($postBounty->getMessages() as $message) {
                                $this->flash->error($message);
                            }
                        }
                    }

                    $href = 'discussion/' . $post->id . '/' . $post->slug . '#C' . $postReply->id;
                    return $this->response->redirect($href);
                }

                foreach ($postReply->getMessages() as $message) {
                    $this->flash->error($message);
                }
            }
        }

        /**
         * Set the post name as title - escaping it first
         */
        $this->tag->setTitle($this->escaper->escapeHtml($post->title) . ' - Discussion');

        $this->view->post = $post;
    }

    /**
     * Shows the latest modification made to a post
     */
    public function historyAction($id = 0)
    {

        $this->view->disable();

        /**
         * Find the post using get
         */
        $post = Posts::findFirstById($id);
        if (!$post) {
            $this->flashSession->error('The discussion does not exist');
            return $this->response->redirect();
        }

        $a = explode("\n", $post->content);

        $first         = true;
        $parameters    = array('posts_id = ?0', 'bind' => array($post->id), 'order' => 'created_at DESC');

        /** @var PostsHistory[] $postHistories */
        $postHistories = PostsHistory::find($parameters);

        if (count($postHistories) > 1) {
            foreach ($postHistories as $postHistory) {
                if ($first) {
                    $first = false;
                    continue;
                }
                break;
            }
        } else {
            $postHistory = $postHistories->getFirst();
        }

        if (is_object($postHistory)) {
            $b = explode("\n", $postHistory->content);

            $diff     = new \Diff($b, $a, array());
            $renderer = new \Diff_Renderer_Html_SideBySide();

            echo $diff->Render($renderer);
        } else {
            $this->flash->notice('No history available to show');
        }
    }

    protected function checkTokenGetJson()
    {
        $csrfKey = $this->session->get('$PHALCON/CSRF/KEY$');
        $csrfToken = $this->request->getQuery($csrfKey, null, 'dummy');
        if (!$this->security->checkToken($csrfKey, $csrfToken)) {
            return false;
        }
        return true;
    }

    /**
     * Votes a post up
     */
    public function voteUpAction($id = 0)
    {
        $response = new Response();

        if (!$this->checkTokenGetJson()) {
            $csrfTokenError = array(
                'status'  => 'error',
                'message' => 'Token error. This might be CSRF attack.'
            );
            return $response->setJsonContent($csrfTokenError);
        }

        /**
         * Find the post using get
         */
        $post = Posts::findFirstById($id);
        if (!$post) {
            $contentNotExist = array(
                'status'  => 'error',
                'message' => 'Post does not exist'
            );
            return $response->setJsonContent($contentNotExist);
        }

        $user = Users::findFirstById($this->session->get('identity'));
        if (!$user) {
            $contentlogIn = array(
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            );
            return $response->setJsonContent($contentlogIn);
        }

    /*    if ($user->votes <= 0) {
            $contentDontHave = array(
                'status'  => 'error',
                'message' => 'You don\'t have enough votes available'
            );
            return $response->setJsonContent($contentDontHave);
        }*/

        if (PostsVotes::count(array('posts_id = ?0 AND users_id = ?1', 'bind' => array($post->id, $user->id)))) {
            $contentAlreadyVote = array(
                'status'  => 'error',
                'message' => 'You have already voted this post'
            );
            return $response->setJsonContent($contentAlreadyVote);
        }

        $postVote           = new PostsVotes();
        $postVote->posts_id = $post->id;
        $postVote->users_id = $user->id;
        $postVote->vote     = PostsVotes::VOTE_UP;
        if (!$postVote->save()) {
            foreach ($postVote->getMessages() as $message) {
                $contentError = array(
                    'status'  => 'error',
                    'message' => $message->getMessage()
                );
                return $response->setJsonContent($contentError);
            }
        }

        $post->votes_up++;
        if ($post->users_id != $user->id) {
            $post->user->increaseKarma(Karma::SOMEONE_DID_VOTE_MY_POST);
            $user->increaseKarma(Karma::VOTE_ON_SOMEONE_ELSE_POST);
        }

        if ($post->save()) {
            $user->votes--;
            if (!$user->save()) {
                foreach ($user->getMessages() as $message) {
                    $contentErrorSave = array(
                        'status'  => 'error',
                        'message' => $message->getMessage()
                    );
                    return $response->setJsonContent($contentErrorSave);
                }
            }
        }

        if ($post->users_id != $user->id) {
            $activity                       = new ActivityNotifications();
            $activity->users_id             = $post->users_id;
            $activity->posts_id             = $post->id;
            $activity->posts_replies_id     = null;
            $activity->users_origin_id      = $user->id;
            $activity->type                 = 'P';
            $activity->save();
        }

        $contentOk = array(
            'status' => 'OK'
        );
        return $response->setJsonContent($contentOk);
    }

    /**
     * Votes a post down
     */
    public function voteDownAction($id = 0)
    {
        $response = new Response();

        if (!$this->checkTokenGetJson()) {
            $csrfTokenError = array(
                'status'  => 'error',
                'message' => 'Token error. This might be CSRF attack.'
            );
            return $response->setJsonContent($csrfTokenError);
        }

        /**
         * Find the post using get
         */
        $post = Posts::findFirstById($id);
        if (!$post) {
            $contentNotExist = array(
                'status'  => 'error',
                'message' => 'Post does not exist'
            );
            return $response->setJsonContent($contentNotExist);
        }

        $user = Users::findFirstById($this->session->get('identity'));
        if (!$user) {
            $contentlogIn = array(
                'status'  => 'error',
                'message' => 'You must log in first to vote'
            );
            return $response->setJsonContent($contentlogIn);
        }

        if ($user->votes <= 0) {
            $contentDontHave = array(
                'status'  => 'error',
                'message' => 'You don\'t have enough votes available'
            );
            return $response->setJsonContent($contentDontHave);
        }

        if (PostsVotes::count(array('posts_id = ?0 AND users_id = ?1', 'bind' => array($post->id, $user->id)))) {
            $contentAlreadyVote = array(
                'status'  => 'error',
                'message' => 'You have already voted this post'
            );
            return $response->setJsonContent($contentAlreadyVote);
        }

        $postVote           = new PostsVotes();
        $postVote->posts_id = $post->id;
        $postVote->users_id = $user->id;
        $postVote->vote     = PostsVotes::VOTE_DOWN;
        $postVote->save();

        $post->votes_down++;
        if ($post->users_id != $user->id) {
            $post->user->decreaseKarma(Karma::SOMEONE_DID_VOTE_MY_POST);
            $user->increaseKarma(Karma::VOTE_ON_SOMEONE_ELSE_POST);
        }

        if ($post->save()) {
            $user->votes--;
            if (!$user->save()) {
                foreach ($user->getMessages() as $message) {
                    $contentErrorSave = array(
                        'status'  => 'error',
                        'message' => $message->getMessage()
                    );
                    return $response->setJsonContent($contentErrorSave);
                }
            }
        }

        $contentOk = array(
            'status' => 'OK'
        );
        return $response->setJsonContent($contentOk);
    }

    public function newcatAction()
    {
	require('/var/www/vokuro/apps/frontend/noutility/scripts/cli-bootstrap.php');
	$database = $di->getShared('db');
	$database->begin();
	$log = new Stream('php://stdout');

	$log->info('Start');
        if ($this->request->isPost()) {

    $category               = new Categories();
    $category->assign(array(
                'name' => $this->request->getPost('name', 'striptags'),
                'description' => $this->request->getPost('description'),
                'sub' => $this->request->getPost('sub'),///////////// Au Dela de 3 donness receptionness assign array sinon => 502 Bad Gateway ////////////////////////////
	));
    //$category->name         = $this->request->getPost("name");//$title;
    //$category->description  = $this->request->getPost("description");
    ////$category->sub	    = $this->request->getPost("sub");
    $category->slug         = Tag::friendlyTitle($category->name);//$title
    $category->number_posts = 0;
    $category->no_digest    = "one";
    $category->no_bounty    = "two";
            if (!$category->save()) {
                $this->flash->error($category->getMessages());
		$database->rollback();
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
    public function editcatAction($id)
    {
	require('/var/www/vokuro/apps/frontend/noutility/scripts/cli-bootstrap.php');
        $category = Categories::findFirstById($id);
        if (!$category) {
            $this->flash->error("Category was not found");
            return $this->dispatcher->forward(array(
                'action' => 'index'
            ));
        }

        if ($this->request->isPost()) {

            $category->assign(array(
                'name' => $this->request->getPost('name', 'striptags'),
                'active' => $this->request->getPost('active'),
		'sub' => $this->request->getPost('sub')
            ));

            if (!$category->save()) {
                $this->flash->error($category->getMessages());
            } else {
                $this->flash->success("Category was updated successfully");
            }

            Tag::resetInput();
        }

        $this->view->form = new ProfilesForm($category, array(
            'edit' => true
        ));

        $this->view->category = $category;
    }

    /**
     * Deletes a Profile
     *
     * @param int $id
     */
    public function deletecatAction($id)
    {
	require('/var/www/vokuro/apps/frontend/noutility/scripts/cli-bootstrap.php');
        $profile = Categories::findFirstById($id);
        if (!$profile) {

            $this->flash->error("Category was not found");

            return $this->dispatcher->forward(array(
                'action' => 'index'
            ));
        }

        if (!$profile->delete()) {
            $this->flash->error($profile->getMessages());
        } else {
            $this->flash->success("Category was deleted");
        }

        return $this->dispatcher->forward(array(
            'action' => 'index'
        ));
    }
/*****************************************************************************************/
    /**
     * Shows the latest activity on the IRC
     */
    public function ircAction()
    {

        $parameters = array(
            'order' => 'datelog DESC',
            'limit' => 250
        );
        $irclog = IrcLog::find($parameters);

        $activities = array();
        foreach ($irclog as $log) {
            $who          = explode('@', $log->who);
            $parts        = explode('!', $who[0]);
            $log->who     = substr($parts[0], 1);
            $activities[] = $log;
        }

        $this->view->activities = array_reverse($activities);

        $this->tag->setTitle('Recent Activity on the IRC');
    }

    /**
     * Shows the latest activity on the forum
     */
    public function activityAction($offset = 0)
    {

        $this->view->total = Activities::count();

        $parameters             = array(
            'order' => 'created_at DESC',
            'limit' => array('number' => self::POSTS_IN_PAGE, 'offset' => 0)
        );
        $this->view->activities = Activities::find($parameters);

        $this->tag->setTitle('Recent Activity on the Forum');
    }

    /**
     * Perform the search of posts only searching in the title
     */
    public function searchAction()
    {

        $this->tag->setTitle('Search Results');

        $q = $this->request->getQuery('q');

        $indexer = new Indexer();

        $posts = $indexer->search(array('title' => $q, 'content' => $q), 50, true);
        if (!count($posts)) {
            $posts = $indexer->search(array('title' => $q), 50, true);
            if (!count($posts)) {
                $this->flashSession->notice('There are no search results');
                return $this->response->redirect();
            }
        }

        $paginator = new \stdClass;
        $paginator->count = 0;

        $this->view->posts        = $posts;
        $this->view->totalPosts   = $paginator;
        $this->view->currentOrder = null;
        $this->view->offset       = 0;
        $this->view->paginatorUri = 'search';
    }

    /**
     *
     */
    public function reloadCategoriesAction()
    {
        $parameters             = array(
            'order' => 'number_posts DESC, name'
        );
        $this->view->categories = Categories::find($parameters);

        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        $this->view->getCache()->delete('sidebar');
    }

    /**
     * Shows the user profile
     */
    public function userAction($id, $username)
    {
        if ($id) {
            $user = Users::findFirstById($id);
        } else {
            $user = Users::findFirstByLogin($username);
            if (!$user) {
                $user = Users::findFirstByName($username);
            }
        }

        if (!$user) {
            $this->flashSession->error('The user does not exist');
            return $this->response->redirect();
        }

        $this->view->user = $user;

        $parametersNumberPosts              = array(
            'users_id = ?0 AND deleted = 0',
            'bind' => array($user->id)
        );
        $this->view->numberPosts = Posts::count($parametersNumberPosts);

        $parametersNumberReplies   = array(
            'users_id = ?0',
            'bind' => array($user->id)
        );
        $this->view->numberReplies = PostsReplies::count($parametersNumberReplies);

        $parametersActivities   = array(
            'users_id = ?0',
            'bind'  => array($user->id),
            'order' => 'created_at DESC',
            'limit' => 15
        );
        $this->view->activities = Activities::find($parametersActivities);

        $users   = Users::find(array('columns' => 'id', 'conditions' => 'karma != 0', 'order' => 'karma DESC'));
        $ranking = count($users);
        foreach ($users as $position => $everyUser) {
            if ($everyUser->id == $user->id) {
                $ranking = $position + 1;
                break;
            }
        }

        $this->view->ranking       = $ranking;
        $this->view->total_ranking = count($users);

        $this->tag->setTitle('Profile - ' . $this->escaper->escapeHtml($user->name));
    }

    public function advancedAction()
    {
	require('/var/www/vokuro/apps/frontend/noutility/scripts/cli-bootstrap.php');
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Phosphorum\Models\Categories', $this->request->getPost());
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = array();
        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $profiles = Categories::find($parameters);
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
/*            'order' => 'datelog DESC',
            'limit' => 250
        );
        $irclog = IrcLog::find($parameters);

        $activities = array();
        foreach ($irclog as $log) {
            $who          = explode('@', $log->who);
            $parts        = explode('!', $who[0]);
            $log->who     = substr($parts[0], 1);
            $activities[] = $log;
        }

        $this->view->activities = array_reverse($activities);

        $this->tag->setTitle('Recent Activity on the IRC');*/
    }

    /**
     * Allow to change your user settings
     */
    public function settingsAction()
    {

        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        $user = Users::findFirstById($usersId);
        if (!$user) {
            $this->flashSession->error('The user does not exist');
            return $this->response->redirect();
        }

        if ($this->request->isPost()) {
            if (!$this->checkTokenPost()) {
                return $this->response->redirect();
            }

            $user->timezone      = $this->request->getPost('timezone');
            $user->notifications = $this->request->getPost('notifications');
            $user->theme         = $this->request->getPost('theme');
            $user->digest        = $this->request->getPost('digest');
            if ($user->save()) {
                $this->session->set('identity-theme', $user->theme);
                $this->session->get('identity-timezone', $user->timezone);
                $this->flashSession->success('Settings were successfully updated');
                return $this->response->redirect();
            }

        } else {
            $this->tag->displayTo('timezone', $user->timezone);
            $this->tag->displayTo('notifications', $user->notifications);
            $this->tag->displayTo('theme', $user->theme);
            $this->tag->displayTo('digest', $user->digest);
        }

        $this->tag->setTitle('My Settings');
        $this->tag->setAutoEscape(false);

        $this->view->user      = $user;
        $this->view->timezones = require APP_PATH .'/frontend/config/timezones.php';

        $parametersNumberPosts   = array(
            'users_id = ?0 AND deleted = 0',
            'bind' => array($user->id)
        );
        $this->view->numberPosts = Posts::count($parametersNumberPosts);

        $parametersNumberReplies   = array(
            'users_id = ?0',
            'bind' => array($user->id)
        );
        $this->view->numberReplies = PostsReplies::count($parametersNumberReplies);
    }

    /**
     * Shows the latest notifications for the current user
     */
    public function notificationsAction($offset = 0)
    {
        $usersId = $this->session->get('identity');
        if (!$usersId) {
            $this->flashSession->error('You must be logged first');
            return $this->response->redirect();
        }

        $user = Users::findFirstById($usersId);
        if (!$user) {
            $this->flashSession->error('The user does not exist');
            return $this->response->redirect();
        }

        $this->view->user = $user;

        $this->view->notifications = ActivityNotifications::find(array(
            'users_id = ?0',
            'bind'  => array($usersId),
            'limit' => 128,
            'order' => 'created_at DESC'
        ));

        $this->tag->setTitle('Notifications');
    }

    /**
     * Finds related posts
     */
    public function findRelatedAction()
    {
        $response = new Response();

        $indexer = new Indexer();
        $results = $indexer->search(array(
            'title' => $this->request->getPost('title')
        ), 5);

        $contentOk = array(
            'status'  => 'OK',
            'results' => $results
        );
        return $response->setJsonContent($contentOk);
    }

    /**
     * Finds related posts
     */
    public function showRelatedAction()
    {
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

        $post = Posts::findFirstById($this->request->getPost('id'));
        if ($post) {
            $indexer = new Indexer();
            $posts = $indexer->search(array(
                'title'    => $post->title,
                'category' => $post->categories_id
            ), 5, true);
            if (count($posts) == 0) {
                $posts = $indexer->search(array(
                    'title'    => $post->title
                ), 5, true);
            }
            $this->view->posts = $posts;
        } else {
            $this->view->posts = array();
        }
    }
}
