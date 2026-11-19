<?php

declare(strict_types=1);

use App\Livewire\Jurisdiction\Edit;
use App\Livewire\Jurisdiction\Index;
use App\Livewire\Jurisdiction\SharedContentEditor;
use App\Livewire\Jurisdiction\Show;
use App\Livewire\Section\EditSection;
use App\Models\Jurisdiction;
use App\Models\Section;
use App\Models\SectionTitle;
use App\Models\SharedContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->section = Section::factory()->create(['text' => '<p>Original text</p>']);
    $this->jurisdiction = $this->section->jurisdiction;
});

describe('public visitors (search page embed)', function () {
    it('can view a jurisdiction read-only', function () {
        Livewire::test(Show::class, ['jurisdiction' => $this->jurisdiction, 'editable' => false])
            ->assertSuccessful()
            ->assertSee('Original text')
            ->assertDontSee('Confirm Delete');
    });

    it('cannot make the component editable', function () {
        Livewire::test(Show::class, ['jurisdiction' => $this->jurisdiction, 'editable' => false])
            ->set('editable', true);
    })->throws(CannotUpdateLockedPropertyException::class);

    it('cannot rename a jurisdiction', function () {
        $originalName = $this->jurisdiction->name;

        Livewire::test(Show::class, ['jurisdiction' => $this->jurisdiction, 'editable' => false])
            ->set('jurisdictionName', 'Hacked')
            ->call('saveJurisdictionName')
            ->assertForbidden();

        expect($this->jurisdiction->fresh()->name)->toBe($originalName);
    });

    it('cannot delete a section', function () {
        Livewire::test(Show::class, ['jurisdiction' => $this->jurisdiction, 'editable' => false])
            ->call('deleteSection', $this->section->id)
            ->assertForbidden();

        expect(Section::find($this->section->id))->not->toBeNull();
    });

    it('cannot open an editor', function (string $target) {
        Livewire::test(Show::class, ['jurisdiction' => $this->jurisdiction, 'editable' => false])
            ->call('toggleEdit', $target === 'section' ? $this->section->id : $target)
            ->assertForbidden();
    })->with(['general', 'section']);

    it('cannot load or save the editing components', function (string $component, Closure $parameters) {
        Livewire::test($component, $parameters->call($this))->assertForbidden();
    })->with([
        'section editor' => [EditSection::class, fn () => ['model' => $this->section, 'parentModel' => $this->jurisdiction]],
        'general information editor' => [Edit::class, fn () => ['jurisdiction' => $this->jurisdiction]],
        'shared content editor' => [SharedContentEditor::class, fn () => []],
    ]);
});

describe('logged-in admins', function () {
    beforeEach(function () {
        $this->actingAs(User::factory()->create());
    });

    it('can rename a jurisdiction', function () {
        Livewire::test(Show::class, ['jurisdiction' => $this->jurisdiction])
            ->set('jurisdictionName', 'new name')
            ->call('saveJurisdictionName')
            ->assertHasNoErrors();

        expect($this->jurisdiction->fresh()->name)->toBe('New Name');
    });

    it('rejects a duplicate jurisdiction name', function () {
        Jurisdiction::factory()->create(['name' => 'Taken']);

        Livewire::test(Show::class, ['jurisdiction' => $this->jurisdiction])
            ->set('jurisdictionName', 'taken')
            ->call('saveJurisdictionName')
            ->assertHasErrors(['jurisdictionName' => 'unique']);
    });

    it('can delete a section', function () {
        Livewire::test(Show::class, ['jurisdiction' => $this->jurisdiction])
            ->call('deleteSection', $this->section->id)
            ->assertDispatched('notify');

        expect(Section::find($this->section->id))->toBeNull();
    });

    it('toggles the general information and section editors', function () {
        Livewire::test(Show::class, ['jurisdiction' => $this->jurisdiction])
            ->call('toggleEdit', 'general')
            ->assertSet('showGeneralInfoEdit', true)
            ->call('toggleEdit', $this->section->id)
            ->assertSet('showGeneralInfoEdit', false)
            ->assertSet('editingSectionId', $this->section->id)
            ->call('toggleEdit', ['sectionId' => $this->section->id])
            ->assertSet('editingSectionId', null);
    });

    it('updates an existing section', function () {
        Livewire::test(EditSection::class, ['model' => $this->section, 'parentModel' => $this->jurisdiction])
            ->assertSet('newSectionText', '<p>Original text</p>')
            ->set('newSectionText', '<p>Updated <script>alert(1)</script></p>')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('sectionUpdated');

        expect($this->section->fresh()->text)->toBe('<p>Updated </p>');
    });

    it('adds a new section to a jurisdiction', function () {
        $title = SectionTitle::factory()->create();

        Livewire::test(EditSection::class, ['model' => new Section, 'parentModel' => $this->jurisdiction])
            ->set('newSectionTitleId', $title->id)
            ->set('newSectionText', '<p>New section</p>')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('sectionAdded');

        expect($this->jurisdiction->sections()->where('section_title_id', $title->id)->value('text'))
            ->toBe('<p>New section</p>');
    });

    it('validates a new section', function () {
        Livewire::test(EditSection::class, ['model' => new Section, 'parentModel' => $this->jurisdiction])
            ->set('newSectionText', '')
            ->call('save')
            ->assertHasErrors(['newSectionTitleId', 'newSectionText']);
    });

    it('locks the section editor\'s model and target', function (string $property) {
        Livewire::test(EditSection::class, ['model' => $this->section, 'parentModel' => $this->jurisdiction])
            ->set($property, 'tampered');
    })->with(['foreignKey', 'parentModel', 'model'])
        ->throws(CannotUpdateLockedPropertyException::class);

    it('saves the general information', function () {
        Livewire::test(Edit::class, ['jurisdiction' => $this->jurisdiction])
            ->set('text', '<p>General <a href="https://example.com">info</a></p>')
            ->call('save')
            ->assertDispatched('toggleEdit');

        expect($this->jurisdiction->fresh()->general_information)
            ->toContain('General')
            ->toContain('target="_blank"');
    });

    it('saves the shared content and clears its cache', function () {
        cache()->forever('shared.jurisdiction.show', 'stale');

        Livewire::test(SharedContentEditor::class)
            ->set('content', '<p>Shared</p><script>x()</script>')
            ->call('save')
            ->assertDispatched('content-saved');

        expect(SharedContent::where('key', 'jurisdiction.show')->value('content'))->toBe('<p>Shared</p>')
            ->and(cache()->has('shared.jurisdiction.show'))->toBeFalse();
    });

    it('returns to the content tab when cancelling shared content edits', function () {
        Livewire::test(SharedContentEditor::class)
            ->call('cancel')
            ->assertRedirect(route('jurisdiction.index'));

        expect(session('active_tab'))->toBe('content');
    });

    it('ignores unknown sort columns and tabs from the query string', function () {
        Livewire::withQueryParams(['sortField' => 'password', 'sortDirection' => 'sideways', 'tab' => 'evil'])
            ->test(Index::class)
            ->assertSuccessful()
            ->assertSet('tab', 'list')
            ->assertSee($this->jurisdiction->name);
    });
});
