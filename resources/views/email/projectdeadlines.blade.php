This is an automatic reminder from ResultPress to remind you that you have an upcoming reporting deadline

Regarding project, {{$details->name}}

You have an upcoming reporting deadline at, {{$project_reminder->set->format('d-m-Y')}}

The report should cover the following activities:

@foreach($activities as $activity)
@if($activity->status() != 'completed' or $activity->status() != 'cancelled' or $activity->status() != 'delayedhigh' or $activity->status() != 'delayednormal')
Activity, {{$activity->title}}, - ends: {{$activity->end->format('d-m-Y')}}
@endif
@endforeach

@foreach($activities as $activity)
@if($delayed_project_reminder->reminder == true)
@once
Additionally, these activities are delayed and should be reported on ASAP:
@endonce

@if($activity->status() == 'delayedhigh' or $activity->status() == 'delayednormal')
Activity, {{$activity->title}}, - ends: {{$activity->end->format('d-m-Y')}}, original reporting deadline was: {{$delayed_project_reminder->set->format('d-m-Y')}}
@endif
@endif
@endforeach
---
Spider – The Swedish Program for Information and Communication Technology in Developing Regions
