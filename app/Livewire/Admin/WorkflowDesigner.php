<?php

namespace App\Livewire\Admin;

use App\Models\Workflow;
use Livewire\Component;

class WorkflowDesigner extends Component
{
    public int $workflowId;
    public string $name = '';
    public string $description = '';
    public array $steps = [];

    // Form for new step
    public string $newStepLabel = '';
    public string $newStepRole = 'pm';
    public string $newStepAction = 'create_task';

    public function mount(int $id)
    {
        $this->workflowId = $id;
        $workflow = Workflow::findOrFail($id);

        $this->name = $workflow->name;
        $this->description = $workflow->description ?? '';
        $this->steps = $workflow->steps ?? [];
    }

    public function addStep()
    {
        if (empty(trim($this->newStepLabel))) {
            session()->flash('error', __('Lütfen bir adım etiketi girin.'));
            return;
        }

        $this->steps[] = [
            'label' => trim($this->newStepLabel),
            'role' => $this->newStepRole,
            'action' => $this->newStepAction,
        ];

        $this->newStepLabel = '';
        $this->newStepRole = 'pm';
        $this->newStepAction = 'create_task';
    }

    public function removeStep(int $index)
    {
        if (isset($this->steps[$index])) {
            array_splice($this->steps, $index, 1);
        }
    }

    public function moveStepUp(int $index)
    {
        if ($index > 0 && isset($this->steps[$index])) {
            $temp = $this->steps[$index];
            $this->steps[$index] = $this->steps[$index - 1];
            $this->steps[$index - 1] = $temp;
        }
    }

    public function moveStepDown(int $index)
    {
        if ($index < count($this->steps) - 1 && isset($this->steps[$index])) {
            $temp = $this->steps[$index];
            $this->steps[$index] = $this->steps[$index + 1];
            $this->steps[$index + 1] = $temp;
        }
    }

    public function saveWorkflow()
    {
        $workflow = Workflow::findOrFail($this->workflowId);
        $workflow->update([
            'name' => $this->name,
            'description' => $this->description,
            'steps' => $this->steps,
        ]);

        session()->flash('message', __('İş akışı başarıyla kaydedildi.'));
        return redirect()->route('admin.resource.index', ['resource' => 'workflows']);
    }

    public function render()
    {
        return view('livewire.admin.workflow-designer')
            ->layout('layouts.admin', [
                'title' => __('Görsel İş Akışı Editörü'),
                'breadcrumb' => __('Workflow Builder'),
            ]);
    }
}
