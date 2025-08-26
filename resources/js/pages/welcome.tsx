import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface Props {
    auth?: {
        user?: {
            id: number;
            name: string;
            email: string;
        };
    };
    canLogin?: boolean;
    canRegister?: boolean;
    stats?: {
        total_rooms: number;
        available_rooms: number;
        occupied_rooms: number;
        maintenance_rooms: number;
        total_reservations: number;
        active_reservations: number;
        todays_checkins: number;
        todays_checkouts: number;
        pending_tasks: number;
        total_guests: number;
    };
    recent_reservations?: Array<{
        id: number;
        confirmation_number: string;
        guest: {
            first_name: string;
            last_name: string;
        };
        room: {
            room_number: string;
            type: string;
        };
        check_in_date: string;
        check_out_date: string;
        status: string;
    }>;
    upcoming_checkins?: Array<{
        id: number;
        confirmation_number: string;
        guest: {
            first_name: string;
            last_name: string;
        };
        room: {
            room_number: string;
        };
        check_in_date: string;
    }>;
    pending_tasks?: Array<{
        id: number;
        room: {
            room_number: string;
        };
        task_type: string;
        priority: string;
    }>;
    [key: string]: unknown;
}

const StatusBadge = ({ status }: { status: string }) => {
    const getStatusColor = (status: string) => {
        switch (status) {
            case 'confirmed': return 'bg-blue-500';
            case 'checked_in': return 'bg-green-500';
            case 'checked_out': return 'bg-gray-500';
            case 'cancelled': return 'bg-red-500';
            default: return 'bg-gray-500';
        }
    };

    return (
        <Badge className={`${getStatusColor(status)} text-white`}>
            {status.replace('_', ' ')}
        </Badge>
    );
};

const PriorityBadge = ({ priority }: { priority: string }) => {
    const getPriorityColor = (priority: string) => {
        switch (priority) {
            case 'urgent': return 'bg-red-600';
            case 'high': return 'bg-orange-500';
            case 'medium': return 'bg-yellow-500';
            case 'low': return 'bg-green-500';
            default: return 'bg-gray-500';
        }
    };

    return (
        <Badge className={`${getPriorityColor(priority)} text-white`}>
            {priority}
        </Badge>
    );
};

const QuickActionButton = ({ action, reservationId, label, variant = 'default' }: {
    action: string;
    reservationId: number;
    label: string;
    variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
}) => {
    const handleAction = () => {
        router.post(route('reservation.actions'), {
            reservation_id: reservationId,
            action: action,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return (
        <Button onClick={handleAction} variant={variant} size="sm">
            {label}
        </Button>
    );
};

export default function Welcome({ 
    auth, 
    canLogin = false, 
    canRegister = false, 
    stats,
    recent_reservations = [],
    upcoming_checkins = [],
    pending_tasks = []
}: Props) {
    if (auth?.user) {
        // Authenticated dashboard view
        return (
            <AppShell>
                <Head title="Hotel Management Dashboard" />
                
                <div className="space-y-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h1 className="text-3xl font-bold text-gray-900">🏨 Hotel Management Dashboard</h1>
                            <p className="text-gray-600 mt-2">Manage your hotel operations efficiently</p>
                        </div>
                        <div className="flex space-x-3">
                            <Link href={route('reservations.create')}>
                                <Button>📋 New Reservation</Button>
                            </Link>
                            <Link href={route('guests.create')}>
                                <Button variant="outline">👤 Add Guest</Button>
                            </Link>
                        </div>
                    </div>

                    {/* Statistics Cards */}
                    {stats && (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <Card>
                                <CardHeader className="pb-2">
                                    <CardTitle className="text-sm font-medium">🏠 Total Rooms</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="text-2xl font-bold">{stats.total_rooms}</div>
                                </CardContent>
                            </Card>
                            
                            <Card>
                                <CardHeader className="pb-2">
                                    <CardTitle className="text-sm font-medium">✅ Available</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="text-2xl font-bold text-green-600">{stats.available_rooms}</div>
                                </CardContent>
                            </Card>
                            
                            <Card>
                                <CardHeader className="pb-2">
                                    <CardTitle className="text-sm font-medium">🔴 Occupied</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="text-2xl font-bold text-red-600">{stats.occupied_rooms}</div>
                                </CardContent>
                            </Card>
                            
                            <Card>
                                <CardHeader className="pb-2">
                                    <CardTitle className="text-sm font-medium">📋 Active Reservations</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="text-2xl font-bold">{stats.active_reservations}</div>
                                </CardContent>
                            </Card>
                            
                            <Card>
                                <CardHeader className="pb-2">
                                    <CardTitle className="text-sm font-medium">🧹 Pending Tasks</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="text-2xl font-bold text-orange-600">{stats.pending_tasks}</div>
                                </CardContent>
                            </Card>
                        </div>
                    )}

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {/* Today's Activity */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center">📅 Today's Activity</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="flex justify-between items-center">
                                    <span className="text-sm text-gray-600">Check-ins</span>
                                    <span className="font-semibold text-blue-600">{stats?.todays_checkins || 0}</span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-sm text-gray-600">Check-outs</span>
                                    <span className="font-semibold text-green-600">{stats?.todays_checkouts || 0}</span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-sm text-gray-600">Maintenance Rooms</span>
                                    <span className="font-semibold text-orange-600">{stats?.maintenance_rooms || 0}</span>
                                </div>
                            </CardContent>
                        </Card>

                        {/* Recent Reservations */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center justify-between">
                                    📋 Recent Reservations
                                    <Link href={route('reservations.index')}>
                                        <Button variant="outline" size="sm">View All</Button>
                                    </Link>
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {recent_reservations.length > 0 ? recent_reservations.map((reservation) => (
                                    <div key={reservation.id} className="border rounded-lg p-3">
                                        <div className="flex justify-between items-start mb-2">
                                            <div>
                                                <p className="font-medium">{reservation.guest.first_name} {reservation.guest.last_name}</p>
                                                <p className="text-sm text-gray-500">Room {reservation.room.room_number}</p>
                                            </div>
                                            <StatusBadge status={reservation.status} />
                                        </div>
                                        <p className="text-sm text-gray-600">
                                            {new Date(reservation.check_in_date).toLocaleDateString()} - {new Date(reservation.check_out_date).toLocaleDateString()}
                                        </p>
                                        {reservation.status === 'confirmed' && (
                                            <div className="flex space-x-2 mt-2">
                                                <QuickActionButton
                                                    action="checkin"
                                                    reservationId={reservation.id}
                                                    label="Check In"
                                                    variant="default"
                                                />
                                            </div>
                                        )}
                                        {reservation.status === 'checked_in' && (
                                            <div className="flex space-x-2 mt-2">
                                                <QuickActionButton
                                                    action="checkout"
                                                    reservationId={reservation.id}
                                                    label="Check Out"
                                                    variant="outline"
                                                />
                                            </div>
                                        )}
                                    </div>
                                )) : (
                                    <p className="text-gray-500">No recent reservations</p>
                                )}
                            </CardContent>
                        </Card>

                        {/* Upcoming Check-ins */}
                        <Card>
                            <CardHeader>
                                <CardTitle>🔔 Upcoming Check-ins</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-3">
                                {upcoming_checkins.length > 0 ? upcoming_checkins.map((checkin) => (
                                    <div key={checkin.id} className="border rounded-lg p-3">
                                        <p className="font-medium">{checkin.guest.first_name} {checkin.guest.last_name}</p>
                                        <p className="text-sm text-gray-500">Room {checkin.room.room_number}</p>
                                        <p className="text-sm text-blue-600 font-medium">
                                            {new Date(checkin.check_in_date).toLocaleDateString()}
                                        </p>
                                    </div>
                                )) : (
                                    <p className="text-gray-500">No upcoming check-ins</p>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Pending Housekeeping Tasks */}
                    {pending_tasks.length > 0 && (
                        <Card>
                            <CardHeader>
                                <CardTitle>🧹 Pending Housekeeping Tasks</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    {pending_tasks.map((task) => (
                                        <div key={task.id} className="border rounded-lg p-3">
                                            <div className="flex justify-between items-start mb-2">
                                                <p className="font-medium">Room {task.room.room_number}</p>
                                                <PriorityBadge priority={task.priority} />
                                            </div>
                                            <p className="text-sm text-gray-600 capitalize">{task.task_type}</p>
                                        </div>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>
                    )}

                    {/* Quick Links */}
                    <Card>
                        <CardHeader>
                            <CardTitle>🚀 Quick Actions</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <Link href={route('rooms.index')}>
                                    <Button variant="outline" className="w-full h-20 flex flex-col">
                                        <span className="text-2xl mb-2">🏠</span>
                                        <span>Manage Rooms</span>
                                    </Button>
                                </Link>
                                <Link href={route('reservations.index')}>
                                    <Button variant="outline" className="w-full h-20 flex flex-col">
                                        <span className="text-2xl mb-2">📋</span>
                                        <span>View Reservations</span>
                                    </Button>
                                </Link>
                                <Link href={route('guests.index')}>
                                    <Button variant="outline" className="w-full h-20 flex flex-col">
                                        <span className="text-2xl mb-2">👥</span>
                                        <span>Guest Directory</span>
                                    </Button>
                                </Link>
                                <Button variant="outline" className="w-full h-20 flex flex-col">
                                    <span className="text-2xl mb-2">📊</span>
                                    <span>Reports</span>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </AppShell>
        );
    }

    // Public welcome page for non-authenticated users
    return (
        <>
            <Head title="Hotel Management System" />
            <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
                {/* Header */}
                <header className="bg-white shadow-sm">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <div className="flex justify-between items-center">
                            <div className="flex items-center">
                                <h1 className="text-2xl font-bold text-gray-900">🏨 HotelManager Pro</h1>
                            </div>
                            <div className="flex space-x-4">
                                {canLogin && (
                                    <Link href={route('login')}>
                                        <Button variant="outline">Log in</Button>
                                    </Link>
                                )}
                                {canRegister && (
                                    <Link href={route('register')}>
                                        <Button>Get Started</Button>
                                    </Link>
                                )}
                            </div>
                        </div>
                    </div>
                </header>

                {/* Hero Section */}
                <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div className="text-center mb-16">
                        <h2 className="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                            🏨 Complete Hotel Management Solution
                        </h2>
                        <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                            Streamline your hotel operations with our comprehensive management system. 
                            Handle reservations, manage rooms, track guests, and oversee housekeeping - all in one place.
                        </p>
                        {canRegister && (
                            <Link href={route('register')}>
                                <Button size="lg" className="mr-4">
                                    Start Free Trial 🚀
                                </Button>
                            </Link>
                        )}
                        {canLogin && (
                            <Link href={route('login')}>
                                <Button variant="outline" size="lg">
                                    Sign In
                                </Button>
                            </Link>
                        )}
                    </div>

                    {/* Features Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                        <Card className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <div className="text-4xl mb-4">📋</div>
                                <CardTitle>Reservation Management</CardTitle>
                                <CardDescription>
                                    Create, modify, and track reservations with ease. Handle check-ins, 
                                    check-outs, and cancellations seamlessly.
                                </CardDescription>
                            </CardHeader>
                        </Card>

                        <Card className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <div className="text-4xl mb-4">🏠</div>
                                <CardTitle>Room Management</CardTitle>
                                <CardDescription>
                                    Comprehensive room inventory with types, pricing, amenities, 
                                    and real-time status tracking.
                                </CardDescription>
                            </CardHeader>
                        </Card>

                        <Card className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <div className="text-4xl mb-4">👥</div>
                                <CardTitle>Guest Profiles</CardTitle>
                                <CardDescription>
                                    Maintain detailed guest information, preferences, and booking 
                                    history for personalized service.
                                </CardDescription>
                            </CardHeader>
                        </Card>

                        <Card className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <div className="text-4xl mb-4">🧹</div>
                                <CardTitle>Housekeeping Tasks</CardTitle>
                                <CardDescription>
                                    Assign and track cleaning, maintenance, and inspection tasks 
                                    with priority management.
                                </CardDescription>
                            </CardHeader>
                        </Card>

                        <Card className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <div className="text-4xl mb-4">💰</div>
                                <CardTitle>Billing & Payments</CardTitle>
                                <CardDescription>
                                    Automated billing calculations, payment tracking, and 
                                    financial reporting capabilities.
                                </CardDescription>
                            </CardHeader>
                        </Card>

                        <Card className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <div className="text-4xl mb-4">📊</div>
                                <CardTitle>Analytics & Reports</CardTitle>
                                <CardDescription>
                                    Comprehensive reporting on occupancy, revenue, guest 
                                    satisfaction, and operational metrics.
                                </CardDescription>
                            </CardHeader>
                        </Card>
                    </div>

                    {/* Benefits Section */}
                    <div className="bg-white rounded-2xl p-8 shadow-lg">
                        <h3 className="text-3xl font-bold text-center mb-8">Why Choose HotelManager Pro?</h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div className="space-y-4">
                                <div className="flex items-start">
                                    <span className="text-2xl mr-3">⚡</span>
                                    <div>
                                        <h4 className="font-semibold">Lightning Fast</h4>
                                        <p className="text-gray-600">Quick operations with instant updates and real-time synchronization.</p>
                                    </div>
                                </div>
                                <div className="flex items-start">
                                    <span className="text-2xl mr-3">🔐</span>
                                    <div>
                                        <h4 className="font-semibold">Secure & Reliable</h4>
                                        <p className="text-gray-600">Enterprise-grade security with regular backups and 99.9% uptime.</p>
                                    </div>
                                </div>
                                <div className="flex items-start">
                                    <span className="text-2xl mr-3">📱</span>
                                    <div>
                                        <h4 className="font-semibold">Mobile Responsive</h4>
                                        <p className="text-gray-600">Access your hotel management system from any device, anywhere.</p>
                                    </div>
                                </div>
                            </div>
                            <div className="space-y-4">
                                <div className="flex items-start">
                                    <span className="text-2xl mr-3">🎯</span>
                                    <div>
                                        <h4 className="font-semibold">User Friendly</h4>
                                        <p className="text-gray-600">Intuitive interface designed for hotel staff with minimal training needed.</p>
                                    </div>
                                </div>
                                <div className="flex items-start">
                                    <span className="text-2xl mr-3">📞</span>
                                    <div>
                                        <h4 className="font-semibold">24/7 Support</h4>
                                        <p className="text-gray-600">Round-the-clock customer support to keep your operations running smoothly.</p>
                                    </div>
                                </div>
                                <div className="flex items-start">
                                    <span className="text-2xl mr-3">💡</span>
                                    <div>
                                        <h4 className="font-semibold">Smart Automation</h4>
                                        <p className="text-gray-600">Automated workflows to reduce manual tasks and prevent errors.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* CTA Section */}
                    <div className="text-center mt-16">
                        <h3 className="text-3xl font-bold mb-4">Ready to Transform Your Hotel Operations?</h3>
                        <p className="text-xl text-gray-600 mb-8">Join thousands of hotels already using HotelManager Pro</p>
                        {canRegister && (
                            <Link href={route('register')}>
                                <Button size="lg">
                                    Get Started Today 🚀
                                </Button>
                            </Link>
                        )}
                    </div>
                </main>
            </div>
        </>
    );
}