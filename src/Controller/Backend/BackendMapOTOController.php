<?php
namespace App\Controller\Backend;
use App\Entity\Map\Map;
use App\Utils\Helper\MapControllerHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/map/edit_oto')]
class BackendMapOTOController extends AbstractController
{
    use BackendControllerTrait;

    #[Route(path: '/{relationship}/{id}', name: 'admin_map_oto_edit')]
    public function editRelationshipAction(Request $request, Map $entity, string $relationship, EntityManagerInterface $em, ParameterBagInterface $bag): Response
    {
        $type= MapControllerHelper::OTO_FORM_TYPE[$relationship];
        $class= MapControllerHelper::OTO_RELATIONSHIP[$relationship];
        $inst= $this->getInstanceOrNewRelationship($em, $class, $entity);

        $twigArgs=[
            'relationship'=>$relationship,
            'relEntity'=>$inst,
            'relType'=>'oto'
        ];

        return call_user_func_array([$this, '_updateRequest'], [
            'request'=>$request,
            'entity'=>$entity,
            'controller'=>$this,
            'form'=> $this->createForm($type, $inst)->handleRequest($request),
            'em'=>$em,
            'view'=>'@admin/map/edit.html.twig',
            'twigArgs'=>$twigArgs
        ]);
    }
    
    

    #[Route(path: '/delete/{relationship}/{id}', name: 'admin_map_oto_delete',
        requirements: ['id' => '\w+', 'relationship' => '(\w+)'])]
    public function deleteRelationshipAction(Request $request, Map $entity, string $relationship,  EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete_oto_'.$relationship.'_'.$entity->getId(), $request->get('_token'))) {
            try{
                $class= MapControllerHelper::OTO_RELATIONSHIP[$relationship];
                $repo= $em->getRepository($class);
                $res= $repo->findBy(['map'=>$entity]);

                if(!count($res)) {
                    $this->addFlash('danger', 'No data found for relationship ' . $relationship);
                }else{
                    $em->remove($res[0]);
                    $em->flush();
                    $this->addFlash('success', $translator->trans('msg.delete.success', [], 'cavemessages') );
                }
            }catch(\Exception $ex){
                $this->addFlash('danger', $ex->getMessage() );
            }
        }else{
            $this->addFlash('danger', $translator->trans('form.invalidtoken', [], 'validators'));
        }


        return $this->redirectToRoute('admin_map_oto_edit', ['id'=>$entity->getId(), 'relationship'=>$relationship]);

    }

    private function getInstanceOrNewRelationship(EntityManagerInterface $em, string $class, Map $map): object
    {

        $res= $em->getRepository($class)->findBy(['map'=>$map]);
            return (!count($res))? new $class($map) : $res[0];
    }
}