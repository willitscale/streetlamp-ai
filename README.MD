# Streetlamp AI

Attribute router tooling for AI solutions. A support framework for rapidly building MCP servers using attributes in PHP.

## Table of Contents

- [Getting Started](#getting-started)
- [Defining your MCP Server](#defining-your-mcp-server)
- [Defining Capabilities](#defining-the-mcp-capabilities-and-sub-capabilities)
- [Defining Method Actions](#defining-method-actions)
- [Usage Example](#usage-example)

## Prerequisites

- PHP 8.4 or higher
- Composer

# Getting started 

To begin with you're going to need to add Streetlamp AI to your composer package with:

```bash
composer require willitscale/streetlamp-ai
```

This will include Streetlamp and Streetlamp AI into your package.

# Creating your first MCP Server

I'd advise you to read through the documentation for [Streetlamp](https://github.com/willitscale/streetlamp) and getting a Streetlamp application up and running first.
I'm going to skip what's covered in the Streetlamp documentation for getting setup.

## Defining our MCP Server

What we need to do first is to define our server, we can do this with the attribute `ModelContextProtocol` which will bootstrap an MCP server for us at the defined path provided.
The class we decorate with the `ModelContextProtocol` can have middleware applied to it as shown below.

```php
#[ModelContextProtocol('/mcp', McpVersion::LATEST, 'Streetlamp', '1.0.0', 'Client information')]
#[Middleware(AuthMiddleware::class)]
class McpController
{
}
```

Unlike a standard controller within Streetlamp the requests are passed off to a catch-all method within the `McpHandler`.
If you would like to handle all requests yourself you can do so by extending the `McpHandler` and overriding the `call` method as shown below.

```php
#[ModelContextProtocol('/mcp', McpVersion::LATEST, 'Streetlamp', '1.0.0', 'Client information')]
#[Middleware(AuthMiddleware::class)]
class McpController extends McpHandler
{
    public function call(
        #[BodyParameter] Request $request,
        #[HeaderParameter('Accept', true)] string $accept,
        #[HeaderParameter('MCP-Protocol-Version', true)] string $mcpProtocolVersion,
        #[HeaderParameter('MCP-Session-Id')] ?string $mcpSessionId = null
    ): ResponseInterface {
        ...
    }
}
```

## Defining the MCP Capabilities and Sub-Capabilities

With the server defined we now need to define what capabilities our server will offer.
We define capabilities by decorating a class with the `McpCapability` attribute and the corresponding capability. 
Within out capabilities we can also define sub-capabilities by decorating a function with the attribute `McpSubCapability` to be used as a callback for that sub-capability.

```php
#[McpCapability(McpCapabilities::RESOURCES)]
class ResourcesCapability
{
    #[McpSubCapability(McpSubCapabilities::SUBSCRIBE)]
    public function subscribe(): mixed
    {
        ...
    }
}
```

I'd advise to read through the [MCP Specification](https://modelcontextprotocol.io/specification/2025-06-18) to understand how to respond correctly to MCP clients.

## Defining Method Actions

When making requests to the MCP server you do so via JsonRpc requests which require a method.
This method gets mapped to a capability and then an action within that capability.
To define an action you decorate a method within a capability with the `McpAction` attribute as shown below.

```php
#[McpCapability(McpCapabilities::RESOURCES)]
class ResourcesCapability
{
    #[McpAction('list')]
    public function resources(): array
    {
        return [
            'resources' => [
                ...
            ] 
        ];
    }
    
    #[McpAction('read')]
    public function resources(): array
    {
        return [
            'contents' => [
                ...
            ] 
        ];
    }
    
    #[McpAction('templates/list')]
    public function resources(): array
    {
        return [
            'resourceTemplates' => [
                ...
            ] 
        ];
    }
}
```

Responses from the action function calls are wrapped in the JsonRpc `Response` object as the result.
