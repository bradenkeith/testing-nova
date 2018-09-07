@component('mail::message')
#Project Access Granted
###You have been given access to project "{{ $project->name }}".

Hi {{ $emailAddress-> name }},

To access the files for this project, please use the link below.

@component('mail::button', ['url' => $url])
View Project
@endcomponent

This is an unmonitored email box, please do not respond to this email.
@endcomponent
