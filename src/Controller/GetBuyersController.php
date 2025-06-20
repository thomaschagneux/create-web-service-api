<?php

namespace App\Controller;

use App\Dto\PaginationQuery;
use App\Entity\ApiUser;
use App\Service\BuyerService;
use OpenApi\Attributes as OA;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class GetBuyersController extends AbstractController
{
    public function __construct(
        private readonly TagAwareCacheInterface $cache,
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * Cette méthode permet de récupérer l'ensemble des acheteurs liés à l'utilisateur actuel.
     *
     * @throws InvalidArgumentException
     */
    #[Route('/api/buyers', name: 'get_buyers', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return list of buyers ',
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
    #[OA\Tag('Buyers')]
    #[IsGranted('ROLE_ADMIN')]
    public function listUsers(Request $request, BuyerService $buyerService): JsonResponse
    {
        $pageQuery = $request->query->get('page', '1');
        $limitQuery = $request->query->get('limit', '100');
        /** @var ApiUser $apiUser */
        $apiUser = $this->getUser();

        $paginationQuery = new PaginationQuery(
            (string) $pageQuery,
            (string) $limitQuery,
        );

        $errors = $this->validator->validate($paginationQuery);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        $idCache = 'list_users_page_'.$paginationQuery->getPage().'_limit_'.$paginationQuery->getLimit();
        $isCached = 'true';

        $jsonUserList = $this->cache->get($idCache, function (ItemInterface $item) use ($paginationQuery, $buyerService, &$isCached, $apiUser) {
            $isCached = 'false';
            $item->expiresAfter(60 * 5);

            return $buyerService->getSerializedBuyers($paginationQuery->getPageAsInt(), $paginationQuery->getLimitAsInt(), $apiUser);
        });

        return new JsonResponse($jsonUserList, 200, ['x-is-cached' => $isCached], true);
    }
}
