<?php

/*
 * This file is part of the ACME PHP library.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AcmePhp\Core;

use AcmePhp\Core\Exception\AcmeCoreClientException;
use AcmePhp\Core\Exception\AcmeCoreServerException;
use AcmePhp\Core\Protocol\Challenge;
use AcmePhp\Ssl\CertificateRequest;
use AcmePhp\Ssl\CertificateResponse;
use AcmePhp\Ssl\KeyPair;

/**
 * ACME protocol client interface.
 *
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
interface AcmeClientInterface
{
    /**
     * Register the local account KeyPair in the Certificate Authority.
     *
     * @param string|null $agreement An optionnal URI referring to a subscriber agreement or terms of service.
     * @param string|null $email     An optionnal e-mail to associate with the account.
     *
     * @throws AcmeCoreServerException When the ACME server returns an error HTTP status code
     *                                 (the exception will be more specific if detail is provided).
     * @throws AcmeCoreClientException When an error occured during response parsing.
     *
     * @return array The Certificate Authority response decoded from JSON into an array.
     */
    public function registerAccount($agreement = null, $email = null);

    /**
     * Request challenge data for a given domain.
     *
     * A Challenge is an association between a URI, a token and a payload.
     * The Certificate Authority will create this challenge data and
     * you will then have to expose the payload for the verification
     * (see requestCheck).
     *
     * @param string $domain The domain to challenge.
     *
     * @throws AcmeCoreServerException When the ACME server returns an error HTTP status code
     *                                 (the exception will be more specific if detail is provided).
     * @throws AcmeCoreClientException When an error occured during response parsing.
     *
     * @return Challenge The data returned by the Certificate Authority.
     */
    public function requestChallenge($domain);

    /**
     * Ask the Certificate Authority to check given challenge data.
     *
     * This check will generally consists of requesting over HTTP the domain
     * at a specific URL. This URL should return the raw payload generated
     * by requestChallenge.
     *
     * WARNING : This method SHOULD NOT BE USED in a web action. It will
     * wait for the Certificate Authority to validate the check and this
     * operation could be long.
     *
     * @param Challenge $challenge The challenge data to check.
     * @param int       $timeout   The timeout period.
     *
     * @throws AcmeCoreServerException When the ACME server returns an error HTTP status code
     *                                 (the exception will be more specific if detail is provided).
     * @throws AcmeCoreClientException When an error occured during response parsing.
     *
     * @return bool Was the check successful?
     */
    public function checkChallenge(Challenge $challenge, $timeout = 180);

    /**
     * Request a certificate for the given domain.
     *
     * This method should be called only if the previous check challenge has
     * been successful.
     *
     * WARNING : This method SHOULD NOT BE USED in a web action. It will
     * wait for the Certificate Authority to validate the certificate and
     * this operation could be long.
     *
     * @param string             $domain        The domain to request a certificate for.
     * @param KeyPair            $domainKeyPair The domain SSL KeyPair to use (for renewal).
     * @param CertificateRequest $csr           The Certificate Signing Request (informations for the certificate).
     * @param int                $timeout       The timeout period.
     *
     * @throws AcmeCoreServerException When the ACME server returns an error HTTP status code
     *                                 (the exception will be more specific if detail is provided).
     * @throws AcmeCoreClientException When an error occured during response parsing.
     *
     * @return CertificateResponse The certificate data to save it somewhere you want.
     */
    public function requestCertificate($domain, KeyPair $domainKeyPair, CertificateRequest $csr, $timeout = 180);
}
