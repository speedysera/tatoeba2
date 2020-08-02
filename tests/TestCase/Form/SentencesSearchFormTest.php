<?php

use App\Form\SentencesSearchForm;
use Cake\TestSuite\TestCase;

class SentencesSearchFormTest extends TestCase
{
    public $fixtures = [
        'app.users',
        'app.sentences_lists',
        'app.tags',
        'app.users_languages',
    ];

    public function setUp() {
        parent::setUp();
        $this->Form = new SentencesSearchForm();
        $this->Search = $this->createTestProxy(\App\Model\Search::class);
        $this->Form->setSearch($this->Search);
    }

    public function tearDown() {
        parent::tearDown();
        unset($this->Form);
    }

    public function testDefaultData() {
        $expected = [
            'query' => '',
            'to' => 'und',
            'from' => 'und',
            'unapproved' => 'no',
            'orphans' => 'no',
            'user' => '',
            'has_audio' => '',
            'tags' => '',
            'list' => '',
            'native' => '',
            'trans_filter' => 'limit',
            'trans_to' => 'und',
            'trans_link' => '',
            'trans_has_audio' => '',
            'trans_unapproved' => '',
            'trans_orphan' => '',
            'trans_user' => '',
            'sort' => 'relevance',
            'sort_reverse' => '',
        ];
        $this->Form->setData([]);
        $this->assertEquals($expected, $this->Form->getData());
    }

    public function testUnknownParam() {
        $this->Form->setData(['unknown_param' => 'super strange']);
        $this->assertNull($this->Form->getData('unknown_param'));
    }

    public function searchParamsProvider() {
        return [
            [ 'query',
              'your proposal',
              ['filterByQuery', 'your proposal' ],
              'your proposal'
            ],
            [ 'query',
              '散りぬるを　我が世誰ぞ',
              ['filterByQuery', '散りぬるを 我が世誰ぞ' ],
              '散りぬるを 我が世誰ぞ'
            ],
            [ 'query',
              "ceci\u{a0}; cela\u{a0}",
              ['filterByQuery', 'ceci ; cela ' ],
              'ceci ; cela '
            ],

            [ 'from', 'ain',         ['filterByLanguage', 'ain'        ], 'ain' ],
            [ 'from', '',            ['filterByLanguage', ''           ], 'und' ],
            [ 'from', 'invalidlang', ['filterByLanguage', 'invalidlang'], 'und' ],

            [ 'to', 'und',     [], 'und' ],
            [ 'to', 'none',    [], 'none' ],
            [ 'to', 'fra',     [], 'fra' ],
            [ 'to', '',        [], 'und' ],
            [ 'to', 'invalid', [], 'und' ],

            [ 'unapproved', 'yes',     ['filterByCorrectness', true],  'yes' ],
            [ 'unapproved', 'no',      ['filterByCorrectness', false], 'no'  ],
            [ 'unapproved', 'invalid', ['filterByCorrectness', null],  ''    ],
            [ 'unapproved', '',        ['filterByCorrectness', null],  ''    ],

            [ 'orphans', 'yes',     ['filterByOrphanship', true],  'yes' ],
            [ 'orphans', 'no',      ['filterByOrphanship', false], 'no'  ],
            [ 'orphans', 'invalid', ['filterByOrphanship', null],  ''    ],
            [ 'orphans', '',        ['filterByOrphanship', null],  ''    ],

            [ 'user', 'contributor', ['filterByOwnerId', 4], 'contributor' ],
            [ 'user', 'invaliduser', ['filterByOwnerId'],    '', 1 ],
            [ 'user', '',            ['filterByOwnerId'],    '' ],

            [ 'has_audio', 'yes',     ['filterByAudio', true],  'yes' ],
            [ 'has_audio', 'no',      ['filterByAudio', false], 'no'  ],
            [ 'has_audio', 'invalid', ['filterByAudio', null],  ''    ],
            [ 'has_audio', '',        ['filterByAudio', null],  ''    ],

            [ 'tags', 'OK',          ['filterByTags', ['OK']],            'OK'    ],
            [ 'tags', 'invalid tag', ['filterByTags', ['invalid tag']],   '',   1 ],
            [ 'tags', 'OK,invalid',  ['filterByTags', ['OK', 'invalid']], 'OK', 1 ],

            [ 'list', '2',       ['filterByListId', 2, null],       '2'   ],
            [ 'list', '9999999', ['filterByListId', 9999999, null], '', 1 ],
            [ 'list', '',        ['filterByListId', null, null],    ''    ],
            [ 'list', '3',       ['filterByListId', 3, null],       '', 1 ],

            [ 'native', 'yes',     ['filterByNativeSpeaker', true],  'yes' ],
            [ 'native', 'no',      ['filterByNativeSpeaker', null],  ''    ],
            [ 'native', 'invalid', ['filterByNativeSpeaker', null],  ''    ],
            [ 'native', '',        ['filterByNativeSpeaker', null],  ''    ],

            [ 'trans_filter', 'exclude',      ['filterByTranslation', 'exclude'], 'exclude' ],
            [ 'trans_filter', 'invalidvalue', ['filterByTranslation'], 'limit' ],

            [ 'trans_to', 'ain',     ['filterByTranslationLanguage', 'ain'    ], 'ain' ],
            [ 'trans_to', '',        ['filterByTranslationLanguage', ''       ], 'und' ],
            [ 'trans_to', 'invalid', ['filterByTranslationLanguage', 'invalid'], 'und' ],

            [ 'trans_link', 'direct',   ['filterByTranslationLink', 'direct'],  'direct'],
            [ 'trans_link', 'indirect', ['filterByTranslationLink', 'indirect'],'indirect'],
            [ 'trans_link', '',         ['filterByTranslationLink', ''],        ''],
            [ 'trans_link', 'invalid',  ['filterByTranslationLink', 'invalid'], ''],

            [ 'trans_has_audio', 'yes',     ['filterByTranslationAudio', true],  'yes' ],
            [ 'trans_has_audio', 'no',      ['filterByTranslationAudio', false], 'no'  ],
            [ 'trans_has_audio', 'invalid', ['filterByTranslationAudio', null],  ''    ],
            [ 'trans_has_audio', '',        ['filterByTranslationAudio', null],  ''    ],

            [ 'trans_unapproved', 'yes',     ['filterByTranslationCorrectness', true],  'yes' ],
            [ 'trans_unapproved', 'no',      ['filterByTranslationCorrectness', false], 'no'  ],
            [ 'trans_unapproved', 'invalid', ['filterByTranslationCorrectness', null],  ''    ],
            [ 'trans_unapproved', '',        ['filterByTranslationCorrectness', null],  ''    ],

            [ 'trans_orphan', 'yes',     ['filterByTranslationOrphanship', true],  'yes' ],
            [ 'trans_orphan', 'no',      ['filterByTranslationOrphanship', false], 'no'  ],
            [ 'trans_orphan', 'invalid', ['filterByTranslationOrphanship', null],  ''    ],
            [ 'trans_orphan', '',        ['filterByTranslationOrphanship', null],  ''    ],

            [ 'trans_user', 'contributor', ['filterByTranslationOwnerId', 4], 'contributor' ],
            [ 'trans_user', 'invaliduser', ['filterByTranslationOwnerId'],    '', 1 ],
            [ 'trans_user', '',            ['filterByTranslationOwnerId'],    ''    ],

            [ 'sort', 'relevance', ['sort', 'relevance'], 'relevance' ],
            [ 'sort', 'words',     ['sort', 'words'],     'words'     ],
            [ 'sort', 'modified',  ['sort', 'modified'],  'modified'  ],
            [ 'sort', 'created',   ['sort', 'created'],   'created'   ],
            [ 'sort', 'random',    ['sort', 'random'],    'random'    ],

            [ 'sort_reverse', 'yes',     ['reverseSort', true],  'yes' ],
            [ 'sort_reverse', '',        ['reverseSort', false], '' ],
            [ 'sort_reverse', 'invalid', ['reverseSort', false], '' ],
        ];
    }

    /**
     * @dataProvider searchParamsProvider
     */
    public function testSearchParams($getParam, $getValue, $method, $getParamReturned, $ignored = 0) {
        if (count($method) == 1) {
            $this->Search->expects($this->never())
                         ->method($method[0]);
        } elseif ($method) {
            $methodName = array_shift($method);
            $with = array_map(
                function ($expected) {
                    return $this->callback(function($param) use ($expected) {
                        return $expected === $param;
                    });
                },
                $method
            );
            $this->Search->expects($this->once())
                         ->method($methodName)
                         ->with(...$with);
        }
        $this->Form->setData([$getParam => $getValue]);
        $this->assertEquals($getParamReturned, $this->Form->getData()[$getParam]);

        $this->assertCount($ignored, $this->Form->getIgnoredFields());
    }

    public function testSearchParamToIsCopiedToTransTo_fra() {
        $this->Form->setData(['to' => 'fra']);
        $result = $this->Form->getData();
        $this->assertEquals('fra', $result['to']);
        $this->assertEquals('fra', $result['trans_to']);
    }

    public function testSearchParamToIsCopiedToTransTo_none() {
        $this->Form->setData(['to' => 'none']);
        $result = $this->Form->getData();
        $this->assertEquals('none', $result['to']);
        $this->assertEquals('und',  $result['trans_to']);
    }

    public function testSearchParamToIsCopiedToTransTo_empty() {
        $this->Form->setData(['to' => '']);
        $result = $this->Form->getData();
        $this->assertEquals('und', $result['to']);
        $this->assertEquals('und', $result['trans_to']);
    }

    public function testSearchParamToIsCopiedToTransTo_invalid() {
        $this->Form->setData(['to' => 'invalid']);
        $result = $this->Form->getData();
        $this->assertEquals('und', $result['to']);
        $this->assertEquals('und', $result['trans_to']);
    }

    public function testTransFilter_limitWithoutTransFilters() {
        $this->Search->expects($this->never())
                     ->method('filterByTranslation');
        $this->Form->setData(['trans_filter' => 'limit']);
        $this->assertEquals('limit', $this->Form->getData()['trans_filter']);
    }

    public function testTransFilter_limitWithTranslationFilters() {
        $this->Search->expects($this->once())
                     ->method('filterByTranslation')
                     ->with($this->equalTo('limit'));
        $this->Form->setData(['trans_filter' => 'limit', 'trans_to' => 'hun']);
        $this->assertEquals('limit', $this->Form->getData()['trans_filter']);
    }

    private function assertMethodCalledWith($stub, $methodName, $expectedParams) {
        $stub->expects($this->exactly(count($expectedParams)))
             ->method($methodName)
             ->with($this->callback(
                 function ($param) use (&$expectedParams) {
                     return array_shift($expectedParams) === $param;
                 }
             ));
    }

    public function testSort_invalid() {
        $this->assertMethodCalledWith($this->Search, 'sort', ['invalid', 'relevance']);
        $this->Form->setData(['sort' => 'invalid']);
        $this->assertEquals('relevance', $this->Form->getData()['sort']);
    }

    public function testSort_empty() {
        $this->assertMethodCalledWith($this->Search, 'sort', ['', 'relevance']);
        $this->Form->setData(['sort' => '']);
        $this->assertEquals('relevance', $this->Form->getData()['sort']);
    }

    public function testGetSearchableLists_asGuest() {
        $searcher = null;
        $expected = [
            ['id' => 5, 'user_id' => 1, 'name' => 'Collaborative list'],
            ['id' => 6, 'user_id' => 7, 'name' => 'Inactive list'],
            ['id' => 2, 'user_id' => 7, 'name' => 'Public list'],
        ];
        $this->Form->setData(['list' => '']);
        $result = $this->Form->getSearchableLists($searcher);

        $this->assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    public function testGetSearchableLists_asGuest_withUnlisted() {
        $searcher = null;
        $expected = [
            ['id' => 5, 'user_id' => 1, 'name' => 'Collaborative list'],
            ['id' => 6, 'user_id' => 7, 'name' => 'Inactive list'],
            ['id' => 2, 'user_id' => 7, 'name' => 'Public list'],
            ['id' => 1, 'user_id' => 7, 'name' => 'Interesting French sentences'],
        ];
        $this->Form->setData(['list' => '1']);
        $result = $this->Form->getSearchableLists($searcher);

        $this->assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    public function testGetSearchableLists_asUser() {
        $searcher = 7;
        $expected = [
            ['id' => 5, 'user_id' => 1, 'name' => 'Collaborative list'],
            ['id' => 6, 'user_id' => 7, 'name' => 'Inactive list'],
            ['id' => 1, 'user_id' => 7, 'name' => 'Interesting French sentences'],
            ['id' => 3, 'user_id' => 7, 'name' => 'Private list'],
            ['id' => 2, 'user_id' => 7, 'name' => 'Public list'],
        ];
        $this->Form->setData(['list' => '']);
        $result = $this->Form->getSearchableLists($searcher);

        $this->assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    public function testGetSearchableLists_asUser_withUnlisted() {
        $searcher = 3;
        $expected = [
            ['id' => 5, 'user_id' => 1, 'name' => 'Collaborative list'],
            ['id' => 6, 'user_id' => 7, 'name' => 'Inactive list'],
            ['id' => 2, 'user_id' => 7, 'name' => 'Public list'],
            ['id' => 1, 'user_id' => 7, 'name' => 'Interesting French sentences'],
        ];
        $this->Form->setData(['list' => '1']);
        $result = $this->Form->getSearchableLists($searcher);

        $this->assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    public function testCheckUnwantedCombinations_nop() {
        $this->Form->setData([]);
        $this->Form->checkUnwantedCombinations();
        $this->assertCount(0, $this->Form->getIgnoredFields());
    }

    public function testCheckUnwantedCombinations_orphanWithUser() {
        $this->assertMethodCalledWith(
            $this->Search,
            'filterByOrphanship',
            [true, null]
        );

        $this->Form->setData(['user' => 'contributor', 'orphans' => 'yes']);
        $this->Form->checkUnwantedCombinations();

        $this->assertCount(1, $this->Form->getIgnoredFields());
        $this->assertEquals('', $this->Form->getData()['orphans']);
    }

    public function testCheckUnwantedCombinations_transOrphanWithTransUser() {
        $this->assertMethodCalledWith(
            $this->Search,
            'filterByTranslationOrphanship',
            [true, null]
        );

        $this->Form->setData(['trans_user' => 'contributor', 'trans_orphan' => 'yes']);
        $this->Form->checkUnwantedCombinations();

        $this->assertCount(1, $this->Form->getIgnoredFields());
        $this->assertEquals('', $this->Form->getData()['trans_orphan']);
    }

    public function testCheckUnwantedCombinations_nativeWithoutLanguage() {
        $this->assertMethodCalledWith(
            $this->Search,
            'filterByNativeSpeaker',
            [true, null]
        );

        $this->Form->setData(['from' => 'und', 'native' => 'yes']);
        $this->Form->checkUnwantedCombinations();

        $this->assertCount(1, $this->Form->getIgnoredFields());
        $this->assertEquals('', $this->Form->getData()['native']);
    }

    public function testCheckUnwantedCombinations_userNotNative() {
        $this->assertMethodCalledWith(
            $this->Search,
            'filterByNativeSpeaker',
            [true, null]
        );

        $this->Form->setData([
            'from' => 'eng',
            'user' => 'contributor',
            'native' => 'yes',
        ]);
        $this->Form->checkUnwantedCombinations();

        $this->assertCount(1, $this->Form->getIgnoredFields());
        $this->assertEquals('', $this->Form->getData()['native']);
    }

    public function testAsSphinx() {
        $search = $this->createMock(\App\Model\Search::class);
        $this->Form->setSearch($search);
        $stuff = [ 'sphinx result' ];
        $search->expects($this->once())
               ->method('asSphinx')
               ->willReturn($stuff);

        $result = $this->Form->asSphinx();

        $this->assertEquals($stuff, $result);
    }
}
