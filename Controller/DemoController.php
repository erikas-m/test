<?php

namespace mltest\IssueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Github\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DemoController extends Controller
{
    public function selectUserAction(Request $request)
    {
        $user = $request->request->get("user");
        $client = new Client();
        $users = $client->api('user')->find($user);
        return $this->render('IssueBundle:Default:demoSelectUser.html.twig',
            array(
                'users' => $users['users']
            )
        );

    }

    public function selectRepoAction($user)
    {
        $client = new Client();
        $repos = $client->api('user')->repositories($user);
        return $this->render('IssueBundle:Default:demoSelectRepo.html.twig',
            array(
                'user' => $user,
                'repos' => $repos
            )
        );

    }

    public function indexAction($user, $repo)
    {
        return $this->render('IssueBundle:Default:demoIndex.html.twig',
            array(
                'user' => $user,
                'repo' => $repo
            )
        );
    }
}
