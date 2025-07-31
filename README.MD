# Streetlamp AI

Attribute router tooling for AI solutions. A support framework for rapidly building MCP servers using attributes in PHP.


Example usage:

```php
#[ModelContextProtocol('/mcp', McpVersion::LATEST, 'Streetlamp', '1.0.0', 'Client information')]
#[Middleware(AuthMiddleware::class)]
class McpController
{
    #[McpCapability(McpCapabilities::RESOURCES, true)]
    public function resources(): void
    {
    // TODO ...
    }

    #[McpCapability(McpCapabilities::PROMPTS, true)]
    public function prompts(): void
    {
    // TODO ...
    }
}
```
