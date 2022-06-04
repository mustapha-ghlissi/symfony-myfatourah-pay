<?php

namespace App\Service;

use App\Entity\Promise;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Payment
{
    // Si besoin modifier la clé LIVE
    const API_LIVE_TOKEN = "T4UGWasWQJifzh7QIvJLo74ZJVtjStgkQATDMccotCga5lgVojo2oJtfVOspbCn9s40Fw9ecSDwgiHoNGqPP9NLWEO4cpNlUqTHrYG9m0XXG3Opck7du69-QAS6pC4ilI5WU43kan2IUzxHwwzG8MSX9Oh3PrwF6BKrLjWcVuurUpsdPdHo9tI-CTQI0DpQwDA7iWZrIju9VseAetvrrIg6wrT4ezYGRRBD3oHgPjpPadbprKzRRfw4XmHQxz_8D_7I5PASK8Qu1rGBteYYQ7pjNdXrxt72IN9xo-IohCzhlvbsg8fX5U4L_hkk4A5JGXsOYew1seDJ7dryr07XIe4jDNN2kDFbj05eBcyFMWMbGoAN7Plg4-a0Tn9dNBaFUA3iZ4zD0oRGyih04134gqPLK7BsNlZPGQD-GAMAdPgKqmNhJ0eIcWAAOJZ1aHtyj_1_y6sf1utDHdGwhaAArKSzYqWVMaBk5MmfdbXj6g49ZwNZfQM8plggp4NNZsfKZgKo3sdiHP8OP4rP7K-smXt6Uik2lBcJLYnxTh5aEevPwS6KGKmM0MgVNh2TP-orwWr84XXTfs_P58CxnKqNwzPHKpidIcQ4zkjCVHJDCBHFpeOZLMRmZm4HQE72Jz-bsk5R7HJYe9h52Uw9Oep5X5XuV6HkfdxVtMmMmmLyN54KezU2k85CiEWAyr-Vo_sxrcoItBBbjb-QJ25QH_2m8PN7WYaY";
    const API_TEST_TOKEN = "rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL";
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param User $user
     * @param $amount
     * @param string $currency
     * @return RedirectResponse
     */
    public function pay(User $user, Promise $promise, string $currency = 'SAR'): RedirectResponse {

        $env = strtolower($_ENV['APP_ENV']);

        // Tester l'env si c'est dev ou prod
        if ($env === 'dev') {
            $mfPayment = new PaymentMyfatoorahApiV2(self::API_TEST_TOKEN, true);
        }
        else {
            $mfPayment = new PaymentMyfatoorahApiV2(self::API_LIVE_TOKEN, false);
        }

        // Configuration de paiement
        $fullName = ucfirst($user->getFirst()) . ' ' . strtoupper($user->getLast());
        $postFields = [
            'NotificationOption' => 'Lnk',
            'InvoiceValue'       => $promise->getAmount(),
            'DisplayCurrencyIso' => 'SAR',
            'Language' => 'ER', //EN, AR
            'CustomerName' => $fullName,
            'CustomerEmail' => $user->getEmail(),
            'CustomerMobile' => $user->getPhone(),
            'CallBackUrl' => $this->urlGenerator->generate('adhesion', [
                'id' => $promise->getId()
                ], UrlGeneratorInterface::ABSOLUTE_URL) . '?paymentStatus=success',
            'ErrorUrl' => $this->urlGenerator->generate('adhesion', [
                'id' => $promise->getId()
                ], UrlGeneratorInterface::ABSOLUTE_URL) . '?paymentStatus=failure',
        ];
        $data = $mfPayment->getInvoiceURL($postFields);
        $invoiceId   = $data['invoiceId'];

        // Le lien de paiement vers lequel on sera redirigé
        $paymentLink = $data['invoiceURL'];

        // Redirection vers la page de paiement
        return new RedirectResponse($paymentLink);
    }
}