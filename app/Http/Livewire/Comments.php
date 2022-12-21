<?php

namespace App\Http\Livewire;

use App\Comment;
use App\Project;
use App\Notifications\NewComment;
use Livewire\Component;

class Comments extends Component
{
    public $comments;
    public $comment;
    public $editingCommentId;
    public $editingCommentBody;
    public $project;

    public function mount(Project $project)
    {
        $this->comments = $project->comments;
        $this->project = $project;
    }

    public function addComment()
    {
        $validated = $this->validate([
            'comment' => 'required|max:255',
        ]);

        $comment = Comment::create([
            'body' => $this->comment,
            'meta' => [ 'ip' => request()->ip(), 'user_agent' => request()->userAgent() ],
            'user_id' => auth()->id(),
            'commentable_id' => $this->project->id,
            'commentable_type' => Project::class,
        ]);

        $this->project->project_partner->each(function ($partner) use ($comment) {
            $partner->user->notify(new NewComment($comment, $this->project));
        });

        $this->project->project_owner->each(function ($owner) use ($comment) {
            $owner->user->notify(new NewComment($comment, $this->project));
        });

        $this->comments->push($comment);
        $this->comment = '';
        session()->flash('message', 'Comment successfully added.');
    }

    public function removeComment(Comment $comment)
    {
        $comment->delete();
        $this->comments = $this->comments->where('id', '!=', $comment->id);
    }

    public function editComment(Comment $comment)
    {
        $this->editingCommentId = $comment->id;
        $this->editingCommentBody = $comment->body;
    }

    public function updateComment()
    {
        $validated = $this->validate([
            'editingCommentBody' => 'required|max:255',
        ]);

        $comment = Comment::find($this->editingCommentId);
        $comment->update([
            'body' => $this->editingCommentBody,
        ]);

        $this->comments = $this->comments->map(function ($comment) {
            if ($comment->id === $this->editingCommentId) {
                $comment->body = $this->editingCommentBody;
            }

            return $comment;
        });

        $this->editingCommentId = null;
        $this->editingCommentBody = null;
        // Flash a message, add comment id to session.
        session()->flash('message', sprintf('Comment %s successfully updated.', $comment->id));
    }

    public function cancelEdit()
    {
        $this->editingCommentId = null;
        $this->editingCommentBody = null;
    }

    public function resetErrors()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.comments');
    }
}
