<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PostController extends Controller
{
    /**
     * @Route("/post", name="post")
     */
    public function index()
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    /**
     * @Route("/post/new", methods={"GET", "POST"}, name="post_new")
     */
    public function new(Request $request)
    {
        $post = new Post();

        $post->setCreatedAt(new \DateTime());

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/post/{id}/edit", methods={"GET", "POST"}, name="post_update")
     */
    public function update($id, Request $request)
    {
        $post = $this->getDoctrine()
        ->getRepository(Post::class)
        ->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id ' . $id
                );
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'deleteForm' => $deleteForm->createView()
        ]);
    }

    /**
     * @Route("/post/{id}", methods="GET", name="post_show")
     */
    public function show($id)
    {
        $post = $this->getDoctrine()
        ->getRepository(Post::class)
        ->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id ' . $id
                );
        }

        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/post/{id}", methods="DELETE", name="post_delete")
     */
    public function delete($id, Request $request)
    {
        $post = $this->getDoctrine()
        ->getRepository(Post::class)
        ->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'No post found for id ' . $id
                );
        }

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post');
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
        ->setAction($this->generateUrl('post_delete', ['id' => $id]))
        ->setMethod('DELETE')
        ->getForm();
    }
}
