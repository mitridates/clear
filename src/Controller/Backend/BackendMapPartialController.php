<?php
namespace App\Controller\Backend;
use App\Entity\Map\Map;
use App\Form\backend\Map\Model\PartialFormTypeInterface;
use App\Utils\Helper\MapControllerHelper;
use App\Utils\reflection\EntityReflectionHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/map/edit_partial')]
class BackendMapPartialController extends AbstractController
{
    use BackendControllerTrait;

    #[Route(path: '/{relationship}/{id}', name: 'admin_map_partial_edit')]
    public function editRelationshipAction(Request $request, Map $entity, string $relationship, EntityManagerInterface $em, ParameterBagInterface $bag): Response
    {
        $type= MapControllerHelper::PARTIAL_FORM_TYPE[$relationship];
        $twigArgs=[
            'relationship'=>$relationship,
            'relEntity'=>$entity,
            'relType'=>'partial'
        ];

        return call_user_func_array([$this, '_updateRequest'], [
            'request'=>$request,
            'entity'=>$entity,
            'controller'=>$this,
            'form'=> $this->createForm($type, $entity)->handleRequest($request),
            'em'=>$em,
            'view'=>'@admin/map/edit.html.twig',
            'twigArgs'=>$twigArgs
        ]);
    }
    
    

    #[Route(path: '/delete/{relationship}/{id}', name: 'admin_map_partial_delete',
        requirements: ['id' => '\w+', 'relationship' => '(\w+)'])]
    public function deletePartialAction(Request $request, Map $entity, string $relationship,  EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete_partial_'.$relationship.'_'.$entity->getId(), $request->get('_token')))
        {
            /**@var PartialFormTypeInterface $type */
            $type= MapControllerHelper::PARTIAL_FORM_TYPE[$relationship];
            EntityReflectionHelper::setNullProperties($entity, $type::getFormTypeFieldNames());
            $em->persist($entity);
            $em->flush();
            $em->clear();
        }else{
            $this->addFlash('danger', $translator->trans('form.invalidtoken', [], 'validators'));
        }
        return $this->redirectToRoute('admin_map_partial_edit', ['id'=>$entity->getId(), 'relationship'=>$relationship]);

    }
}