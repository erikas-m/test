<?php

namespace mltest\IssueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Github\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function findAllAction($user, $repo)
    {
        $client = new Client();
        $issues = $client->api('issue')->all($user, $repo, array('state' => 'open'));
        return $this->render('IssueBundle:Default:findAll.html.twig',
            array(
                'issues' => $issues,
                'user' => $user,
                'repo' => $repo
            )
        );
    }

    public function findAction($user, $repo)
    {
        return $this->render('IssueBundle:Default:find.html.twig',
            array(
                'user' => $user,
                'repo' => $repo
            )
        );
    }

    public function createAction($user, $repo, $error)
    {
        return $this->render('IssueBundle:Default:createIssue.html.twig',
            array(
                'user' => $user,
                'repo' => $repo,
                'error' => $error
            )
        );
    }

    public function addAction(Request $request, $user, $repo)
    {
        $login = $request->request->get("login");
        $password = $request->request->get("password");
        $title = $request->request->get("title");
        $body = $request->request->get("body");
        $client = new Client();
        $client->authenticate($login, $password);
        try {
            $issue = $client->api('issue')->create($user, $repo, array('title' => $title, 'body' => $body));
        } catch (\RuntimeException $e){
            return $this->redirect($this->generateUrl('issue_create',
                array(
                    'user'=> $user,
                    'repo' => $repo,
                    'error' => "fail"
                )
            ));
        }
        $num = $issue['number'];
        return $this->redirect($this->generateUrl('issue_show',
            array(
                'user'=> $user,
                'repo' => $repo,
                'num' => $num
            )
        ));
    }

    public function searchAction(Request $request, $user, $repo)
    {
        $term = $request->request->get("term");
        $status = $request->request->get("status");
        $client = new Client();
        $issues = $client->api('issue')->find($user, $repo, $status, $term);
        return $this->render('IssueBundle:Default:results.html.twig',
            array(
                'user' => $user,
                'repo' => $repo,
                'issues' => $issues['issues']
            )
        );
    }

    public function showAction($user, $repo, $num)
    {
        $client = new Client();
        $issue = $client->api('issue')->show($user, $repo, $num);
        return $this->render('IssueBundle:Default:show.html.twig',
            array(
                'issue' => $issue,
                'user' => $user,
                'repo' => $repo
            )
        );
    }

    public function editAction($user, $repo, $num, $error)
    {
        $client = new Client();
        $issue = $client->api('issue')->show($user, $repo, $num);
        return $this->render('IssueBundle:Default:edit.html.twig',
            array(
                'issue' => $issue,
                'user' => $user,
                'repo' => $repo,
                'error' => $error
            )
        );
    }

    public function updateAction(Request $request, $user, $repo, $num)
    {
        $login = $request->request->get("login");
        $state = $request->request->get("status");
        $password = $request->request->get("password");
        $title = $request->request->get("title");
        $body = $request->request->get("body");
        $client = new Client();
        $client->authenticate($login, $password);
        try {
            $issue = $client->api('issue')->update($user, $repo, $num, array('state' => $state, 'title' => $title, 'body' => $body));
        } catch (\RuntimeException $e){
            return $this->redirect($this->generateUrl('issue_edit',
                array(
                    'user'=> $user,
                    'repo' => $repo,
                    'num' => $num,
                    'error' => "fail"
                )
            ));
        }
        $num = $issue['number'];
        return $this->redirect($this->generateUrl('issue_show',
            array(
                'user'=> $user,
                'repo' => $repo,
                'num' => $num
            )
        ));
    }
}
