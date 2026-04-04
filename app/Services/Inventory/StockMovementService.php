<?php

namespace App\Services\Inventory;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Stock;
use App\Models\StockMovementLog;
use RuntimeException;

class StockMovementService
{
    public function reduceFromOrderItems(iterable $items, string $context = 'order_checkout', array $metadata = []): void
    {
        foreach ($items as $item) {
            $this->decrease(
                (int) $item['stock_id'],
                (int) $item['quantity'],
                $context,
                $metadata
            );
        }
    }

    public function reduceFromInvoiceItems(iterable $items, string $context = 'pos_sale', array $metadata = []): void
    {
        foreach ($items as $item) {
            $this->decrease(
                (int) $item['stock_id'],
                (int) $item['quantity'],
                $context,
                $metadata
            );
        }
    }

    public function restoreFromOrder(Order $order, string $context = 'order_cancelled', array $metadata = []): void
    {
        foreach ($order->items as $item) {
            if ($item->stock_id) {
                $this->increase((int) $item->stock_id, (int) $item->quantity, $context, $metadata);
            }
        }
    }

    public function restoreFromInvoice(Invoice $invoice, string $context = 'invoice_reversed', array $metadata = []): void
    {
        foreach ($invoice->items as $item) {
            if ($item->stock_id) {
                $this->increase((int) $item->stock_id, (int) $item->quantity, $context, $metadata);
            }
        }
    }

    public function decrease(int $stockId, int $quantity, string $context = 'general', array $metadata = []): void
    {
        $stock = Stock::query()->lockForUpdate()->findOrFail($stockId);

        if ($quantity < 1) {
            throw new RuntimeException('Stock adjustment quantity must be at least 1.');
        }

        if ($stock->quantity < $quantity) {
            throw new RuntimeException("Insufficient stock for {$stock->name} during {$context}.");
        }

        $beforeQuantity = $stock->quantity;
        $stock->decrement('quantity', $quantity);
        $this->recordMovement($stock->fresh(), 'out', $quantity, $beforeQuantity, $stock->fresh()->quantity, $context, $metadata);
    }

    public function increase(int $stockId, int $quantity, string $context = 'general', array $metadata = []): void
    {
        $stock = Stock::query()->lockForUpdate()->find($stockId);

        if (!$stock || $quantity < 1) {
            return;
        }

        $beforeQuantity = $stock->quantity;
        $stock->increment('quantity', $quantity);
        $this->recordMovement($stock->fresh(), 'in', $quantity, $beforeQuantity, $stock->fresh()->quantity, $context, $metadata);
    }

    protected function recordMovement(
        Stock $stock,
        string $direction,
        int $quantity,
        int $beforeQuantity,
        int $afterQuantity,
        string $context,
        array $metadata = []
    ): void {
        StockMovementLog::create([
            'stock_id' => $stock->id,
            'user_id' => $metadata['user_id'] ?? auth()->id(),
            'direction' => $direction,
            'quantity' => $quantity,
            'before_quantity' => $beforeQuantity,
            'after_quantity' => $afterQuantity,
            'context' => $context,
            'reference_type' => $metadata['reference_type'] ?? null,
            'reference_id' => $metadata['reference_id'] ?? null,
            'notes' => $metadata['notes'] ?? null,
        ]);
    }
}
