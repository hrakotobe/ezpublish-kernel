<?php
/**
 * File contains: eZ\Publish\Core\Repository\Tests\Service\Integration\NameSchemaBase class
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */


namespace eZ\Publish\Core\Repository\Tests\Service\Integration;
use eZ\Publish\Core\Repository\Tests\Service\Integration\Base as BaseServiceTest;
use eZ\Publish\Core\Repository\NameSchemaService;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;

/**
 * Test case for NameSchema service
 */
abstract class NameSchemaBase extends BaseServiceTest
{
    /**
     * Test eZ\Publish\Core\Repository\NameSchemaService method
     * @covers \eZ\Publish\Core\Repository\NameSchemaService::resolveUrlAliasSchema
     */
    public function testResolveUrlAliasSchema()
    {
        $serviceMock = $this->getServiceMock( array( "resolve" ) );

        $content = $this->buildTestContent();

        $serviceMock->expects(
            $this->once()
        )->method(
            "resolve"
        )->with(
            "<urlalias_schema>",
            $this->equalTo( $content->contentType ),
            $this->equalTo( $content->fields ),
            $this->equalTo( $content->versionInfo->languageCodes )
        )->will(
            $this->returnValue( 42 )
        );

        $result = $serviceMock->resolveUrlAliasSchema( $content );

        self::assertEquals( 42, $result );
    }

    /**
     * Test eZ\Publish\Core\Repository\NameSchemaService method
     * @covers \eZ\Publish\Core\Repository\NameSchemaService::resolveUrlAliasSchema
     */
    public function testResolveUrlAliasSchemaFallbackToNameSchema()
    {
        $serviceMock = $this->getServiceMock( array( "resolve" ) );

        $content = $this->buildTestContent( "<name_schema>", "" );

        $serviceMock->expects(
            $this->once()
        )->method(
            "resolve"
        )->with(
            "<name_schema>",
            $this->equalTo( $content->contentType ),
            $this->equalTo( $content->fields ),
            $this->equalTo( $content->versionInfo->languageCodes )
        )->will(
            $this->returnValue( 42 )
        );

        $result = $serviceMock->resolveUrlAliasSchema( $content );

        self::assertEquals( 42, $result );
    }

    /**
     * Test eZ\Publish\Core\Repository\NameSchemaService method
     * @covers \eZ\Publish\Core\Repository\NameSchemaService::resolveNameSchema
     */
    public function testResolveNameSchema()
    {
        $serviceMock = $this->getServiceMock( array( "resolve" ) );

        $content = $this->buildTestContent();

        $serviceMock->expects(
            $this->once()
        )->method(
            "resolve"
        )->with(
            "<name_schema>",
            $this->equalTo( $content->contentType ),
            $this->equalTo( $content->fields ),
            $this->equalTo( $content->versionInfo->languageCodes )
        )->will(
            $this->returnValue( 42 )
        );

        $result = $serviceMock->resolveNameSchema( $content );

        self::assertEquals( 42, $result );
    }

    /**
     * Test eZ\Publish\Core\Repository\NameSchemaService method
     * @covers \eZ\Publish\Core\Repository\NameSchemaService::resolveNameSchema
     */
    public function testResolveNameSchemaWithFields()
    {
        $serviceMock = $this->getServiceMock( array( "resolve" ) );

        $content = $this->buildTestContent();
        $fields = array();
        $fields["text3"]["cro-HR"] = new TextLineValue( "tri" );
        $fields["text1"]["ger-DE"] = new TextLineValue( "ein" );
        $fields["text2"]["ger-DE"] = new TextLineValue( "zwei" );
        $fields["text3"]["ger-DE"] = new TextLineValue( "drei" );
        $mergedFields = $fields;
        $mergedFields["text1"]["cro-HR"] = new TextLineValue( "jedan" );
        $mergedFields["text2"]["cro-HR"] = new TextLineValue( "dva" );
        $mergedFields["text1"]["eng-GB"] = new TextLineValue( "one" );
        $mergedFields["text2"]["eng-GB"] = new TextLineValue( "two" );
        $mergedFields["text3"]["eng-GB"] = new TextLineValue( "" );
        $languages = array( "eng-GB", "cro-HR", "ger-DE" );

        $serviceMock->expects(
            $this->once()
        )->method(
            "resolve"
        )->with(
            "<name_schema>",
            $this->equalTo( $content->contentType ),
            $this->equalTo( $mergedFields ),
            $this->equalTo( $languages )
        )->will(
            $this->returnValue( 42 )
        );

        $result = $serviceMock->resolveNameSchema( $content, $fields, $languages );

        self::assertEquals( 42, $result );
    }

    /**
     * Test eZ\Publish\Core\Repository\NameSchemaService method
     * @covers \eZ\Publish\Core\Repository\NameSchemaService::resolve
     * @dataProvider providerForTestResolve
     */
    public function testResolve( $nameSchema, $expectedName )
    {
        /** @var $service \eZ\Publish\Core\Repository\NameSchemaService */
        $service = $this->repository->getNameSchemaService();

        $content = $this->buildTestContent();

        $name = $service->resolve(
            $nameSchema,
            $content->contentType,
            $content->fields,
            $content->versionInfo->languageCodes
        );

        self::assertEquals( $expectedName, $name );
    }

    /**
     * Test eZ\Publish\Core\Repository\NameSchemaService method
     * @covers \eZ\Publish\Core\Repository\NameSchemaService::resolve
     */
    public function testResolveWithSettings()
    {
        /** @var $service \eZ\Publish\Core\Repository\NameSchemaService */
        $service = $this->repository->getNameSchemaService();

        $this->setConfiguration(
            $service,
            array(
                "limit" => 38,
                "sequence" => "..."
            )
        );

        $content = $this->buildTestContent();

        $name = $service->resolve(
            "Hello, <text1> and <text2> and then goodbye and hello again",
            $content->contentType,
            $content->fields,
            $content->versionInfo->languageCodes
        );

        self::assertEquals(
            array(
                "eng-GB" => "Hello, one and two and then goodbye...",
                "cro-HR" => "Hello, jedan and dva and then goodb..."
            ),
            $name
        );
    }

    /**
     */
    public function providerForTestResolve()
    {
        return array(
            array(
                "<text1>",
                array(
                    "eng-GB" => "one",
                    "cro-HR" => "jedan",
                )
            ),
            array(
                "<text1> <text2>",
                array(
                    "eng-GB" => "one two",
                    "cro-HR" => "jedan dva",
                )
            ),
            array(
                "Hello <text1>",
                array(
                    "eng-GB" => "Hello one",
                    "cro-HR" => "Hello jedan",
                )
            ),
            array(
                "Hello, <text1> and <text2> and then goodbye",
                array(
                    "eng-GB" => "Hello, one and two and then goodbye",
                    "cro-HR" => "Hello, jedan and dva and then goodbye",
                )
            ),
            array(
                "<text1|text2>",
                array(
                    "eng-GB" => "one",
                    "cro-HR" => "jedan",
                )
            ),
            array(
                "<text2|text1>",
                array(
                    "eng-GB" => "two",
                    "cro-HR" => "dva",
                )
            ),
            array(
                "<text3|text1>",
                array(
                    "eng-GB" => "one",
                    "cro-HR" => "jedan",
                )
            ),
            array(
                "<(<text1> <text2>)>",
                array(
                    "eng-GB" => "one two",
                    "cro-HR" => "jedan dva",
                )
            ),
            array(
                "<(<text3|text2>)>",
                array(
                    "eng-GB" => "two",
                    "cro-HR" => "dva",
                )
            ),
            array(
                "<text3|(<text3|text2>)>",
                array(
                    "eng-GB" => "two",
                    "cro-HR" => "dva",
                )
            ),
            array(
                "<text3|(Hello <text2> and <text1>!)>",
                array(
                    "eng-GB" => "Hello two and one!",
                    "cro-HR" => "Hello dva and jedan!",
                )
            ),
            array(
                "<text3|(Hello <text3> and <text1>)|text2>!",
                array(
                    "eng-GB" => "Hello  and one!",
                    "cro-HR" => "Hello  and jedan!",
                )
            ),
            array(
                "<text3|(Hello <text3|text2> and <text1>)|text2>!",
                array(
                    "eng-GB" => "Hello two and one!",
                    "cro-HR" => "Hello dva and jedan!",
                )
            ),
        );
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Field[]
     */
    protected function getFields()
    {
        return array(
            new Field(
                array(
                    "languageCode" => "eng-GB",
                    "fieldDefIdentifier" => "text1",
                    "value" => new TextLineValue( "one" )
                )
            ),
            new Field(
                array(
                    "languageCode" => "eng-GB",
                    "fieldDefIdentifier" => "text2",
                    "value" => new TextLineValue( "two" )
                )
            ),
            new Field(
                array(
                    "languageCode" => "eng-GB",
                    "fieldDefIdentifier" => "text3",
                    "value" => new TextLineValue( "" )
                )
            ),
            new Field(
                array(
                    "languageCode" => "cro-HR",
                    "fieldDefIdentifier" => "text1",
                    "value" => new TextLineValue( "jedan" )
                )
            ),
            new Field(
                array(
                    "languageCode" => "cro-HR",
                    "fieldDefIdentifier" => "text2",
                    "value" => new TextLineValue( "dva" )
                )
            ),
            new Field(
                array(
                    "languageCode" => "cro-HR",
                    "fieldDefIdentifier" => "text3",
                    "value" => new TextLineValue( "" )
                )
            ),
        );
    }

    /**
     * @return \eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition[]
     */
    protected function getFieldDefinitions()
    {
        return array(
            new FieldDefinition(
                array(
                    "id" => "1",
                    "identifier" => "text1",
                    "fieldTypeIdentifier" => "ezstring"
                )
            ),
            new FieldDefinition(
                array(
                    "id" => "2",
                    "identifier" => "text2",
                    "fieldTypeIdentifier" => "ezstring"
                )
            ),
            new FieldDefinition(
                array(
                    "id" => "3",
                    "identifier" => "text3",
                    "fieldTypeIdentifier" => "ezstring"
                )
            ),
        );
    }

    /**
     * Builds stubbed content for testing purpose.
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Content
     */
    protected function buildTestContent( $nameSchema = "<name_schema>", $urlAliasSchema = "<urlalias_schema>" )
    {
        $contentType = new ContentType(
            array(
                "nameSchema" => $nameSchema,
                "urlAliasSchema" => $urlAliasSchema,
                "fieldDefinitions" => $this->getFieldDefinitions()
            )
        );
        $contentInfo = new ContentInfo(
            array(
                "contentType" => $contentType
            )
        );
        $versionInfo = new VersionInfo(
            array(
                "contentInfo" => $contentInfo,
                "languageCodes" => array( "eng-GB", "cro-HR" )
            )
        );

        return new Content(
            array(
                "internalFields" => $this->getFields(),
                "versionInfo" => $versionInfo
            )
        );
    }

    protected function setConfiguration( $service, array $configuration )
    {
        $refObject = new \ReflectionObject( $service );
        $refProperty = $refObject->getProperty( 'settings' );
        $refProperty->setAccessible( true );
        $refProperty->setValue(
            $service,
            $configuration
        );
    }

    /**
     * @var \eZ\Publish\Core\Repository\NameSchemaService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $serviceMock;

    /**
     * Returns service with Repository mock and given $settings.
     *
     * @return \eZ\Publish\Core\Repository\NameSchemaService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getServiceMock( array $methods )
    {
        if ( !isset( $this->serviceMock ) )
        {
            $this->serviceMock = self::getMock(
                "eZ\\Publish\\Core\\Repository\\NameSchemaService",
                $methods,
                array(),
                '',
                false
            );
        }

        return $this->serviceMock;
    }
}
