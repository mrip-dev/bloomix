@component('mail::message')
# New Contact Message Received

**Name:** {{ $data['full_name'] }}  
**Email:** {{ $data['email'] }}  
**Phone:** {{ $data['phone'] ?? 'N/A' }}  
**Subject:** {{ $data['subject'] ?? 'N/A' }}

---

### Message:
{{ $data['message'] ?? 'No message provided' }}

@endcomponent
