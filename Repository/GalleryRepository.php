<?php

namespace Kunstmaan\MediaBundle\Repository;

use Kunstmaan\MediaBundle\Entity\Gallery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * GalleryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GalleryRepository extends EntityRepository
{
	public function save(Gallery $gallery, EntityManager $em)
	{
		$em->persist($gallery);
		$em->flush();
	}
	
	public function delete(Gallery $gallery, EntityManager $em)
	{
		$this->deleteFiles($gallery, $em);
		$this->deleteChildren($gallery, $em);
		$em->remove($gallery);
		$em->flush();
	}
	
	public function deleteFiles(Gallery $gallery, EntityManager $em){
		foreach($gallery->getFiles() as $item){
			$em->remove($item);
		}
	}
	
	public function deleteChildren(Gallery $gallery, EntityManager $em){
		foreach($gallery->getChildren() as $child){
			$this->deleteFiles($child, $em);
			$this->deleteChildren($child, $em);
			$em->remove($child);
		}
	}
	
    public function getAllGalleries($limit = null)
    {
            $qb = $this->createQueryBuilder('gallery')
                       ->select('gallery')
                       ->where('gallery.parent is null');
            if (false === is_null($limit))
                $qb->setMaxResults($limit);

            return $qb->getQuery()
                      ->getResult();
    }
    
    
    public function getGallery($gallery_id, EntityManager $em)
    {
    	$gallery = $em->getRepository('KunstmaanMediaBundle:Gallery')->find($gallery_id);
    	if (!$gallery) {
    		throw $this->createNotFoundException('Unable to find gallery.');
    	}
    	return $gallery;
    }

}