<?php

namespace Zelazowy\Bundle\MemcachedBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MemcachedController extends Controller
{
    public function indexAction()
    {
//        $memcached = new \Memcache();
//        $memcached->addServer('127.0.0.1', '11211');
//        $key = 'active_users';
        
//        if (!$users = $memcached->get($key)) {
            $users = $this->getDoctrine()->getRepository('ZelazowyMemcachedBundle:User')->getActive();
            
//            $memcached->set($key, $users, MEMCACHE_COMPRESSED, 5 * 60);
//        }
        
        return $this->render('ZelazowyMemcachedBundle:Memcached:index.html.twig', array(
            'users' => $users
        ));
    }
}
