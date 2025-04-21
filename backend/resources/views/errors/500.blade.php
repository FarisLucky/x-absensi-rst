{{-- {{ dd($exception->getTraceAsString()) }} --}}
{{ dd($exception->getMessage()) }}
@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', $exception->getMessage())
{{-- @section('message', $exception->getMessage()) --}}
