<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/post', name: 'post.')]
class PostController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(PostRepository $repository): Response
    {
        $posts = $repository->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(
        EntityManagerInterface $entityManager,
        FileUploaderService $fileUploaderService,
        Request $request,
        #[Autowire('%kernel.project_dir%/public/uploads/posts')] string $uploadDirectory
    ): Response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postImage = $form->get('post_image')->getData();
            if ($postImage){
                $newFileName = $fileUploaderService->upload($postImage, $uploadDirectory);
                $post->setImage($newFileName);
            }
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('post.index');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/show/{id}', name: 'show')]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/delete/{id}', name: 'remove')]
    public function delete(Post $post, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($post);
        $entityManager->flush();

        $this->addFlash('success', 'post deleted successfully');

        return $this->redirectToRoute('post.index');
    }
}
