<?php
namespace App\Controller\Backend;

use App\Controller\BackendController;
use App\Domain\Citation\Entity\Citation;
use App\Domain\Citation\Manager\CitationManager;
use App\Domain\JsonApi\Serializers\CitationSerializer;
use App\Form\backend\Citation\BookCarpetType;
use App\Form\backend\Citation\BookType;
use App\Form\backend\Citation\CitationSearchType;
use App\Form\backend\Citation\JournalArticleType;
use App\Form\backend\Citation\WebpageType;
use App\Form\backend\Citation\WebsiteType;
use App\Shared\tobscure\jsonapi\Collection;
use App\Shared\tobscure\jsonapi\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/citation')]
class CitationController extends BackendController
{

    #[Route(path: '/', name: 'admin_citation_index')]
    public function indexAction(Request $request): Response
    {
        $form = $this->createForm(CitationSearchType::class, new Citation());
        return $this->render('@admin/citation/index.html.twig',['form'   => $form->createView()]);
    }

    /**
     * @throws \Exception
     */
    #[Route(path: '/list', name: 'admin_citation_list_json')]
    public function listJsonAction(Request $request, CitationManager $manager,  UrlGeneratorInterface $urlGenerator,): JsonResponse
    {
        $this->acceptOnlyXmlHttpRequest($request);
        $listOptions= $this->getRequestListOptions($request);
        $form= $this->createForm(CitationSearchType::class, new Citation())->handleRequest($request);
        list($paginator, $data) = $manager->paginate($form->getData(), $listOptions);

        $collection = (new Collection($data, new CitationSerializer($urlGenerator)))
                    ->fields(['citation'=>['id','title','subtitle','jsondata']]);

        $document = (new Document($collection));

        $document->addMeta('pagination', $paginator->toArray());

        return new JsonResponse($document , 200, ['Content-Type'=>$document::MEDIA_TYPE]);
    }

    #[Route(path: '/new/{type?}', name: 'admin_citation_new')]
    public function newAction(Request $request, EntityManagerInterface $em, ?string $type): Response
    {
        if(!$type || !$formType=$this->getFormType($type)){
            return $this->render('@admin/citation/types.html.twig');
        }

        if($prefix = $request->get('toggle'))
        {
            $oldForm= $this->createForm($this->getFormType($prefix), new Citation())->handleRequest($request);
            return $this->render('@admin/citation/new.html.twig', [
                'form'=>$this->createForm($formType, $oldForm->getData())->createView(),
                'type'=>$type
            ]);
        }

        $form= $this->createForm($this->getFormType($type), new Citation())->handleRequest($request);

        return call_user_func_array([$this, '_createRequest'], [
            'form'=>$form,
            'em'=>$em,
            'controller'=>$this,
            'routeEdit'=>'admin_citation_edit',
            'viewNew'=>'@admin/citation/new.html.twig',
            'twigArgs'=>['type'=>$type]
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'admin_citation_edit')]
    public function editAction(Request $request, Citation $entity, EntityManagerInterface $em): Response
    {
        $formType= $this->getFormType($entity->typeToString());

        $form= $this->createForm($formType, $entity)->handleRequest($request);

        if (!$request->isXmlHttpRequest()){
            return $this->render(-'@admin/citation/edit.html.twig', ['form' => $form->createView(), 'entity' => $entity, 'type'=>$entity->typeToString()]);
        }

        if (!$form->isSubmitted() || !$form->isValid()){
            return $this->getJsonFormErrorResponse($form);
        }

        try{
            $em->persist($form->getData());
            $em->flush();
            return new JsonResponse(null , 200);
        }catch (\Exception $e){
            return $this->getJsonExceptionErrorResponse($e);
        }

    }

    #[Route(path: '/citation/{id}/delete', name: 'admin_citation_delete')]
    public function deleteAction(Request $request, Citation $entity, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        return call_user_func_array([$this, 'CommonBackendDeleteAction'], array_merge(func_get_args(), [
            'routeSuccess'=>'admin_citation_index',
            'routeError'=>'admin_citation_edit',
        ]));
    }

    private function getFormType(string|int $type): string
    {
        return match ($type) {
            Citation::BOOK_TYPE, 'book' => BookType::class,
            Citation::BOOK_CARPET_TYPE, 'book-carpet', 'book_carpet' => BookCarpetType::class,
            Citation::JOURNAL_ARTICLE_TYPE, 'journal-article', 'journal_article' => JournalArticleType::class,
            Citation::WEBPAGE_TYPE, 'webpage' => WebpageType::class,
            Citation::WEBSITE_TYPE, 'website'=> WebsiteType::class,
            Citation::ONLINE_ARTICLE_TYPE, 'online_article', 'online-article' => WebpageType::class,
           // Citation::ONLINE_MAGAZINE_ARTICLE_TYPE, 'online_magazine_article' => false,
            default => false,
        };
    }
}