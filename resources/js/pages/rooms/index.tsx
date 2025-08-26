import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface Room {
    id: number;
    room_number: string;
    type: string;
    price_per_night: number;
    status: string;
    capacity: number;
    amenities: string[];
    description: string;
}

interface Props {
    rooms: {
        data: Room[];
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
        meta: {
            total: number;
            per_page: number;
            current_page: number;
        };
    };
    [key: string]: unknown;
}

const StatusBadge = ({ status }: { status: string }) => {
    const getStatusColor = (status: string) => {
        switch (status) {
            case 'available': return 'bg-green-500';
            case 'occupied': return 'bg-red-500';
            case 'maintenance': return 'bg-orange-500';
            case 'cleaning': return 'bg-blue-500';
            default: return 'bg-gray-500';
        }
    };

    return (
        <Badge className={`${getStatusColor(status)} text-white`}>
            {status}
        </Badge>
    );
};

const RoomTypeIcon = ({ type }: { type: string }) => {
    const icons = {
        single: '🛏️',
        double: '🛏️🛏️',
        suite: '🏨',
        deluxe: '✨',
        presidential: '👑'
    };
    
    return <span className="text-2xl">{icons[type as keyof typeof icons] || '🏠'}</span>;
};

export default function RoomsIndex({ rooms }: Props) {
    return (
        <AppShell>
            <Head title="Room Management" />
            
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-3xl font-bold text-gray-900">🏠 Room Management</h1>
                        <p className="text-gray-600 mt-2">Manage hotel rooms, pricing, and availability</p>
                    </div>
                    <Link href={route('rooms.create')}>
                        <Button>+ Add New Room</Button>
                    </Link>
                </div>

                {/* Room Statistics */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm font-medium">Total Rooms</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{rooms.meta?.total || rooms.data.length}</div>
                        </CardContent>
                    </Card>
                    
                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm font-medium">Available</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-green-600">
                                {rooms.data.filter(room => room.status === 'available').length}
                            </div>
                        </CardContent>
                    </Card>
                    
                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm font-medium">Occupied</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-red-600">
                                {rooms.data.filter(room => room.status === 'occupied').length}
                            </div>
                        </CardContent>
                    </Card>
                    
                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-sm font-medium">Maintenance</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-orange-600">
                                {rooms.data.filter(room => room.status === 'maintenance' || room.status === 'cleaning').length}
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Rooms Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    {rooms.data.map((room) => (
                        <Card key={room.id} className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center space-x-3">
                                        <RoomTypeIcon type={room.type} />
                                        <div>
                                            <CardTitle className="text-lg">Room {room.room_number}</CardTitle>
                                            <CardDescription className="capitalize">
                                                {room.type.replace('_', ' ')} • {room.capacity} guests
                                            </CardDescription>
                                        </div>
                                    </div>
                                    <StatusBadge status={room.status} />
                                </div>
                            </CardHeader>
                            
                            <CardContent className="space-y-4">
                                <div className="text-2xl font-bold text-green-600">
                                    ${room.price_per_night}/night
                                </div>
                                
                                {room.amenities && room.amenities.length > 0 && (
                                    <div>
                                        <p className="text-sm font-medium mb-2">Amenities:</p>
                                        <div className="flex flex-wrap gap-1">
                                            {room.amenities.slice(0, 3).map((amenity, index) => (
                                                <Badge key={index} variant="outline" className="text-xs">
                                                    {amenity}
                                                </Badge>
                                            ))}
                                            {room.amenities.length > 3 && (
                                                <Badge variant="outline" className="text-xs">
                                                    +{room.amenities.length - 3} more
                                                </Badge>
                                            )}
                                        </div>
                                    </div>
                                )}

                                <div className="flex space-x-2">
                                    <Link href={route('rooms.show', room.id)} className="flex-1">
                                        <Button variant="outline" size="sm" className="w-full">
                                            View Details
                                        </Button>
                                    </Link>
                                    <Link href={route('rooms.edit', room.id)}>
                                        <Button size="sm">Edit</Button>
                                    </Link>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                {/* No rooms message */}
                {rooms.data.length === 0 && (
                    <Card className="text-center py-12">
                        <CardContent>
                            <div className="text-6xl mb-4">🏠</div>
                            <h3 className="text-xl font-semibold mb-2">No rooms found</h3>
                            <p className="text-gray-600 mb-6">Start by adding your first room to the hotel inventory.</p>
                            <Link href={route('rooms.create')}>
                                <Button>Add First Room</Button>
                            </Link>
                        </CardContent>
                    </Card>
                )}

                {/* Pagination */}
                {rooms.links && (
                    <div className="flex justify-center space-x-2">
                        {rooms.links.map((link, index) => (
                            <Link
                                key={index}
                                href={link.url || '#'}
                                className={`px-3 py-2 rounded-md ${
                                    link.active 
                                        ? 'bg-blue-500 text-white' 
                                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                } ${!link.url ? 'opacity-50 cursor-not-allowed' : ''}`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </AppShell>
    );
}