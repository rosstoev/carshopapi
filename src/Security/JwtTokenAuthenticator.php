<?php
declare(strict_types=1);

namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;

class JwtTokenAuthenticator extends AbstractGuardAuthenticator
{

    private JWTEncoderInterface $encoder;
    private EntityManagerInterface $em;
    private UserRepository $userRepo;

    public function __construct(JWTEncoderInterface $encoder, EntityManagerInterface $em, UserRepository $userRepo)
    {
        $this->encoder = $encoder;
        $this->em = $em;
        $this->userRepo = $userRepo;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supports(Request $request)
    {
        return $request->headers->get('Authorization') ?? false;
    }

    public function getCredentials(Request $request)
    {
        $extractor = new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization');

        $token = $extractor->extract($request);

        if (!$token) {
            return;
        }

        return $token;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (empty($credentials)) {
            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }

        $data = $this->encoder->decode($credentials);

        if ($data == false) {
            throw new CustomUserMessageAuthenticationException('Invalid Token');
        }

        $email = $data['username'];

        return $this->userRepo->findOneBy(['email' => $email]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {

    }

    public function supportsRememberMe()
    {
       return false;
    }
}