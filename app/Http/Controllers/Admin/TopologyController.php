<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NetworkLink;
use App\Models\NetworkNode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TopologyController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Topology/Index');
    }

    public function data(): JsonResponse
    {
        $nodes = NetworkNode::all()->map(fn ($n) => $n->toReactFlowNode());
        $links = NetworkLink::with(['sourceNode', 'targetNode'])->get()->map(fn ($l) => $l->toReactFlowEdge());

        return response()->json(['nodes' => $nodes, 'links' => $links]);
    }

    public function storeNode(Request $request): JsonResponse
    {
        $node = NetworkNode::create($request->validate([
            'name'      => 'required|string|max:100',
            'type'      => 'required|string',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'position'  => 'nullable|array',
        ]));

        return response()->json($node->toReactFlowNode(), 201);
    }

    public function storeLink(Request $request): JsonResponse
    {
        $link = NetworkLink::create($request->validate([
            'source_node_id' => 'required|exists:network_nodes,id',
            'target_node_id' => 'required|exists:network_nodes,id',
            'type'           => 'nullable|string',
            'media_type'     => 'nullable|string',
        ]));

        return response()->json($link->toReactFlowEdge(), 201);
    }
}
