<?php

namespace App\Controller;

use Aws\S3\S3Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MediaController
 * @package App\Controller
 * @Route("/media");
 */
class MediaController extends AbstractController
{


    private $s3BucketName = '';
    public function __construct( string $s3BucketName )
    {
        $this->s3BucketName = $s3BucketName;
    }

    /**
     * @Route("/", name="s3_index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(S3Client $s3)
    {
        $result = $s3->listBuckets([]);

        return $this->render('media/index.html.twig', [
            'root' => $this->s3BucketName,
            'buckets' => $result->get('Buckets'),
            'controller_name' => 'MediaController',
        ]);
    }

    /**
     * @Route("/bucket/{name}", name="s3_bucket")
     * @IsGranted("ROLE_ADMIN")
     */
    public function bucketList(S3Client $s3, $name='')
    {

// register a 's3://' wrapper with the official AWS SDK
        $s3->registerStreamWrapper();

        $finder = new Finder();
        $bucket = $name ?: $this->s3BucketName;

        $result = $s3->listObjects(['Bucket' => $name]);
        return $this->render('media/list.html.twig', [
            'files' => $finder->in('s3://' . $bucket)
        ]);
    }

}
