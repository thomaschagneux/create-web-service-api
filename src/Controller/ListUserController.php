<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Service\UserService;
use OpenApi\Attributes as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class ListUserController extends AbstractController
{
    public function __construct(
        private readonly TagAwareCacheInterface $cache,
    ) {
    }

    /**
     * Cette méthode permet de récupérer l'ensemble des utilisateurs liés à un client.
     *
     * @throws InvalidArgumentException
     */
    #[Route('/api/customer/{id}/user-list', name: 'list_user_by_customer', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return list of users ',
    )]
    #[OA\Parameter(
        name: 'id',
        description: "L'identifiant du client",
        in: 'path',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'The page we want to retrieve',
        in: 'query',
        schema: new OA\Schema(type: 'int')
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'The number of elements per page',
        in: 'query',
        schema: new OA\Schema(type: 'int')
    )]
    #[OA\Tag('Users')]
    #[IsGranted('ROLE_ADMIN')]
    public function listUsers(Customer $customer, Request $request, UserService $userService): JsonResponse
    {
        $page = $request->query->get('page', '1');
        $limit = $request->query->get('limit', '20');

        if (!ctype_digit($page) || !ctype_digit($limit)) {
            return new JsonResponse(['message' => 'Page or limit is not numeric'], 400);
        }

        $idCache = 'list_users_customer_'.$customer->getId().'_page_'.$page.'_limit_'.$limit;
        $isCached = 'true';

        $jsonUserList = $this->cache->get($idCache, function (ItemInterface $item) use ($customer, $limit, $page, $userService, $isCached) {
            $isCached = 'false';
            $item = $item->expiresAfter(60 * 5);

            return $userService->getUsers((int) $page, (int) $limit, $customer);
        });

        return new JsonResponse($jsonUserList, 200, ['x-is-cached' => $isCached], true);
    }
}
