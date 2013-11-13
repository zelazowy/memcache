<?php

namespace Zelazowy\Bundle\MemcachedBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Zelazowy\Bundle\MemcachedBundle\Entity\User;
use Zelazowy\Bundle\MemcachedBundle\Form\UserType;

class MemcachedController extends Controller
{
    public function indexAction()
    {
        /* @var $memcached \Memcache */
        $memcached = $this->get('memcached');
        $key = 'active_users';
        
        if (!$users = $memcached->get($key)) {
            $users = $this->getDoctrine()->getRepository('ZelazowyMemcachedBundle:User')->getActive();
            
            $memcached->set($key, $users, MEMCACHE_COMPRESSED, 1 * 60);
        }
        
        return $this->render('ZelazowyMemcachedBundle:Memcached:index.html.twig', array(
            'users' => $users
        ));
    }
    
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);
        
        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();
                
                // clearing memcache active users list
                /* @var $memcached \Memcache */
                $memcached = $this->get('memcached');
                $key = 'active_users';
                
                $memcached->delete($key);
                
                return $this->redirect($this->generateUrl('zelazowy_memcached_homepage'));
            }
        }
        
        return $this->render('ZelazowyMemcachedBundle:Memcached:new.html.twig', array('form' => $form->createView()));
    }
}
