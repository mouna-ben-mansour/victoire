<?php
// src/Kunstmaan/AdminBundle/controller/PictureController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Helper\MediaHelper;
use Kunstmaan\MediaBundle\Form\SlideType;
use Kunstmaan\MediaBundle\Entity\Slide;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * picture controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class SlideController extends Controller
{
    /**
     * @Route("/{media_id}", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_slide_show")
     * @Template()
     */
    public function showAction($media_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $media = $em->find('\Kunstmaan\MediaBundle\Entity\Slide', $media_id);
        $gallery = $media->getGallery();
        $galleries = $em->getRepository('KunstmaanMediaBundle:SlideGallery')
                                ->getAllGalleries();

        return array(
                    'media' => $media,
                    'gallery' => $gallery,
                    'galleries' => $galleries
                );
    }

    /**
     * @Route("/delete/{media_id}", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_slide_delete")
     */
    public function deleteAction($media_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $media = $em->find('\Kunstmaan\MediaBundle\Entity\Media', $media_id);
        $gallery = $media->getGallery();

        $em->remove($media);
        $em->flush();

        return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
    }

    /**
     * @Route("/{gallery_id}/create", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_slide_create")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function createAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $gallery = $this->getSlideGallery($gallery_id, $em);

        $request = $this->getRequest();
        $slide = new Slide();
        $slide->setGallery($gallery);
        $form = $this->createForm(new SlideType(), $slide);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($slide);
                $em->flush();

                return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:SlideGallery')
                        ->getAllGalleries();
        return array(
            'form' => $form->createView(),
            'gallery' => $gallery,
            'galleries' => $galleries
        );
    }

    /**
     * @Route("/{media_id}/edit", requirements={"media_id" = "\d+"}, name="KunstmaanMediaBundle_slide_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($media_id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $slide = $em->getRepository('KunstmaanMediaBundle:Media')->find($media_id);
        $slide->setContent($slide->getUuid());
        $request = $this->getRequest();
        $form = $this->createForm(new SlideType(), $slide);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                $slide->setUuid($slide->getContent());
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($slide);
                $em->flush();

                return new \Symfony\Component\HttpFoundation\RedirectResponse($this->generateUrl('KunstmaanMediaBundle_slide_show', array( 'media_id' => $slide->getId() )));
            }
        }

        $galleries = $em->getRepository('KunstmaanMediaBundle:SlideGallery')
                        ->getAllGalleries();
        return array(
            'form' => $form->createView(),
            'media' => $slide,
            'gallery' => $slide->getGallery(),
            'galleries' => $galleries
        );
    }

    protected function getSlideGallery($gallery_id, \Doctrine\ORM\EntityManager $em)
    {
        $imagegallery = $em->getRepository('KunstmaanMediaBundle:SlideGallery')->find($gallery_id);

        if (!$imagegallery) {
            throw $this->createNotFoundException('Unable to find slide gallery.');
        }

        return $imagegallery;
    }



}