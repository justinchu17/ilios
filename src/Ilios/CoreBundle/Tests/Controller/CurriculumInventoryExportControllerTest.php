<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Class CurriculumInventoryExportControllerTest
 * @package Ilios\CoreBundle\Tests\Controller
 */
class CurriculumInventoryExportControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryExportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryInstitutionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceData',
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'createdAt'
        ];
    }

    /**
     * @covers Ilios\CoreBundle\Controller\CurriculumInventoryExportController::postAction
     * @group controllers_a
     */
    public function testPostCurriculumInventoryExport()
    {
        $postData = $this->container->get('ilioscore.dataloader.curriculuminventoryexport')->create();

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryexports'),
            json_encode(['curriculumInventoryExport' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true)['curriculumInventoryExports'][0];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals($responseData['report'], $postData['report']);
        $this->assertNotEmpty($responseData['createdBy']);
        $this->assertNotEmpty($responseData['createdAt']);
        $this->assertFalse(array_key_exists('document', $responseData), 'Document is not part of payload.');
    }
}
